import { Editor, Extension, Mark, mergeAttributes } from '@tiptap/core';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';

function notify(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type },
    }));
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

/**
 * Perataan teks untuk paragraf dan heading.
 */
const TextAlign = Extension.create({
    name: 'textAlign',

    addOptions() {
        return {
            types: ['heading', 'paragraph'],
            alignments: ['left', 'center', 'right', 'justify'],
        };
    },

    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    textAlign: {
                        default: null,
                        parseHTML: (element) => element.style.textAlign || null,
                        renderHTML: (attributes) => {
                            if (! attributes.textAlign || attributes.textAlign === 'left') {
                                return {};
                            }

                            return { style: `text-align: ${attributes.textAlign}` };
                        },
                    },
                },
            },
        ];
    },

    addCommands() {
        return {
            setTextAlign: (alignment) => ({ commands }) => {
                if (! this.options.alignments.includes(alignment)) {
                    return false;
                }

                // updateAttributes() mengembalikan false untuk tipe node yang
                // tidak sedang aktif, jadi semua tipe harus dicoba satu per
                // satu (every() berhenti di false pertama dan membuat
                // perataan paragraf tidak pernah diterapkan).
                let applied = false;

                for (const type of this.options.types) {
                    applied = commands.updateAttributes(type, { textAlign: alignment }) || applied;
                }

                return applied;
            },
            unsetTextAlign: () => ({ commands }) => {
                return this.options.types.every((type) => commands.resetAttributes(type, 'textAlign'));
            },
        };
    },
});

/**
 * Warna teks (disimpan sebagai <span style="color: ...">).
 */
const TextColor = Mark.create({
    name: 'textColor',

    addAttributes() {
        return {
            color: {
                default: null,
                parseHTML: (element) => element.style.color || null,
                renderHTML: (attributes) => (attributes.color ? { style: `color: ${attributes.color}` } : {}),
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'span[style]',
                getAttrs: (element) => (element.style.color ? null : false),
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return ['span', mergeAttributes(HTMLAttributes), 0];
    },
});

/**
 * Ukuran teks (disimpan sebagai <span style="font-size: ...">).
 */
const FontSize = Mark.create({
    name: 'fontSize',

    addAttributes() {
        return {
            fontSize: {
                default: null,
                parseHTML: (element) => element.style.fontSize || null,
                renderHTML: (attributes) => (attributes.fontSize ? { style: `font-size: ${attributes.fontSize}` } : {}),
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'span[style]',
                getAttrs: (element) => (element.style.fontSize ? null : false),
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return ['span', mergeAttributes(HTMLAttributes), 0];
    },
});

/**
 * Stabilo teks (disimpan sebagai <mark>).
 */
const Highlight = Mark.create({
    name: 'highlight',

    addAttributes() {
        return {
            color: {
                default: null,
                parseHTML: (element) => element.style.backgroundColor || null,
                renderHTML: (attributes) => (attributes.color ? { style: `background-color: ${attributes.color}` } : {}),
            },
        };
    },

    parseHTML() {
        return [{ tag: 'mark' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['mark', mergeAttributes(HTMLAttributes), 0];
    },
});

export function tiptapEditor(initialContent = '', uploadUrl = '') {
    let editor = null;

    return {
        active: {
            bold: false,
            italic: false,
            underline: false,
            strike: false,
            code: false,
            heading1: false,
            heading2: false,
            heading3: false,
            heading4: false,
            bulletList: false,
            orderedList: false,
            blockquote: false,
            codeBlock: false,
            link: false,
            highlight: false,
            block: 'p',
            alignment: 'left',
            color: '',
            fontSize: '',
        },

        stats: {
            words: 0,
            characters: 0,
        },

        uploading: false,

        fullscreen: false,

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [1, 2, 3, 4] },
                        link: {
                            openOnClick: false,
                            autolink: true,
                            HTMLAttributes: {
                                rel: 'noopener noreferrer nofollow',
                                target: '_blank',
                            },
                        },
                    }),
                    TextAlign,
                    TextColor,
                    FontSize,
                    Highlight,
                    Image.configure({
                        inline: false,
                        allowBase64: false,
                        HTMLAttributes: { class: 'article-image' },
                    }),
                    Placeholder.configure({
                        placeholder: 'Tulis isi artikel di sini...',
                    }),
                ],
                content: initialContent || '',
                onUpdate: ({ editor: currentEditor }) => {
                    this.syncContent(currentEditor);
                },
                onSelectionUpdate: () => this.updateActive(),
                onTransaction: () => this.updateActive(),
            });

            this.$wire?.set('isi', editor.getHTML(), false);
            this.updateActive();
            this.updateStats();
        },

        destroy() {
            editor?.destroy();
            editor = null;
            this.fullscreen = false;
            document.documentElement.style.overflow = '';
        },

        syncContent(currentEditor = editor) {
            if (! currentEditor) {
                return;
            }

            this.$wire?.set('isi', currentEditor.getHTML(), false);
            this.updateStats();
        },

        updateStats() {
            if (! editor) {
                return;
            }

            const text = editor.getText().trim();

            this.stats = {
                words: text === '' ? 0 : text.split(/\s+/).length,
                characters: text.length,
            };
        },

        updateActive() {
            if (! editor) {
                return;
            }

            const isHeading = (level) => editor.isActive('heading', { level });
            const block = isHeading(1) ? 'h1'
                : isHeading(2) ? 'h2'
                    : isHeading(3) ? 'h3'
                        : isHeading(4) ? 'h4'
                            : 'p';

            const nodeAttributes = editor.getAttributes(isHeading(1) || isHeading(2) || isHeading(3) || isHeading(4) ? 'heading' : 'paragraph');
            const colorAttributes = editor.getAttributes('textColor');
            const fontSizeAttributes = editor.getAttributes('fontSize');

            this.active = {
                bold: editor.isActive('bold'),
                italic: editor.isActive('italic'),
                underline: editor.isActive('underline'),
                strike: editor.isActive('strike'),
                code: editor.isActive('code'),
                heading1: isHeading(1),
                heading2: isHeading(2),
                heading3: isHeading(3),
                heading4: isHeading(4),
                bulletList: editor.isActive('bulletList'),
                orderedList: editor.isActive('orderedList'),
                blockquote: editor.isActive('blockquote'),
                codeBlock: editor.isActive('codeBlock'),
                link: editor.isActive('link'),
                highlight: editor.isActive('highlight'),
                block,
                alignment: nodeAttributes.textAlign || 'left',
                color: colorAttributes.color || '',
                fontSize: fontSizeAttributes.fontSize || '',
            };
        },

        toggleBold() {
            editor?.chain().focus().toggleBold().run();
        },

        toggleItalic() {
            editor?.chain().focus().toggleItalic().run();
        },

        toggleUnderline() {
            editor?.chain().focus().toggleUnderline().run();
        },

        toggleStrike() {
            editor?.chain().focus().toggleStrike().run();
        },

        toggleCode() {
            editor?.chain().focus().toggleCode().run();
        },

        toggleHighlight() {
            if (! editor) {
                return;
            }

            if (editor.isActive('highlight')) {
                editor.chain().focus().unsetMark('highlight').run();

                return;
            }

            editor.chain().focus().setMark('highlight', { color: '#fde68a' }).run();
        },

        toggleHeading(level) {
            editor?.chain().focus().toggleHeading({ level }).run();
        },

        setBlockType(value) {
            if (! editor) {
                return;
            }

            if (value === 'p') {
                editor.chain().focus().setParagraph().run();

                return;
            }

            const level = Number.parseInt(value.replace('h', ''), 10);

            if (Number.isInteger(level) && level >= 1 && level <= 4) {
                editor.chain().focus().setHeading({ level }).run();
            }
        },

        toggleBulletList() {
            editor?.chain().focus().toggleBulletList().run();
        },

        toggleOrderedList() {
            editor?.chain().focus().toggleOrderedList().run();
        },

        toggleBlockquote() {
            editor?.chain().focus().toggleBlockquote().run();
        },

        toggleCodeBlock() {
            editor?.chain().focus().toggleCodeBlock().run();
        },

        insertHorizontalRule() {
            editor?.chain().focus().setHorizontalRule().run();
        },

        setTextAlign(alignment) {
            editor?.chain().focus().setTextAlign(alignment).run();
        },

        setColor(color) {
            editor?.chain().focus().setMark('textColor', { color }).run();
        },

        unsetColor() {
            editor?.chain().focus().unsetMark('textColor').run();
        },

        setFontSize(fontSize) {
            if (! editor) {
                return;
            }

            if (fontSize === '') {
                editor.chain().focus().unsetMark('fontSize').run();

                return;
            }

            editor.chain().focus().setMark('fontSize', { fontSize }).run();
        },

        clearFormatting() {
            editor?.chain().focus().unsetAllMarks().clearNodes().run();
        },

        undo() {
            editor?.chain().focus().undo().run();
        },

        redo() {
            editor?.chain().focus().redo().run();
        },

        setLink() {
            if (! editor) {
                return;
            }

            const previous = editor.getAttributes('link').href ?? '';
            const input = window.prompt('Masukkan URL tautan:', previous);

            if (input === null) {
                return;
            }

            if (input.trim() === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();

                return;
            }

            const url = /^(https?:\/\/|mailto:|tel:|\/)/i.test(input)
                ? input
                : `https://${input}`;

            editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        },

        insertImage() {
            this.$refs.imageInput?.click();
        },

        /**
         * Fokuskan editor saat area kosong di dalam kotak editor diklik,
         * bukan hanya saat teksnya yang diklik.
         */
        focusEditor(event) {
            if (! editor) {
                return;
            }

            if (event.target instanceof Element && event.target.closest('.ProseMirror')) {
                return;
            }

            editor.chain().focus('end').run();
        },

        toggleFullscreen() {
            this.fullscreen = ! this.fullscreen;
            document.documentElement.style.overflow = this.fullscreen ? 'hidden' : '';
        },

        async uploadImage(event) {
            const file = event.target.files?.[0];

            if (! file) {
                return;
            }

            // Aturan sama dengan config/cms.php (image): hanya JPG, maksimal 2 MB.
            if (file.type !== 'image/jpeg') {
                notify('Format gambar harus JPG.', 'danger');
                event.target.value = '';

                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                notify('Ukuran gambar maksimal 2 MB.', 'danger');
                event.target.value = '';

                return;
            }

            const form = new FormData();
            form.append('image', file);

            this.uploading = true;

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        Accept: 'application/json',
                    },
                    body: form,
                });

                const data = await response.json().catch(() => ({}));

                if (! response.ok) {
                    throw new Error(
                        data.errors?.image?.[0]
                        ?? data.message
                        ?? 'Gagal mengunggah gambar.',
                    );
                }

                editor?.chain().focus().setImage({
                    src: data.url,
                    alt: file.name,
                }).run();
            } catch (error) {
                notify(error.message || 'Gagal mengunggah gambar.', 'danger');
            } finally {
                this.uploading = false;
                event.target.value = '';
            }
        },
    };
}
