<template>
  <div class="mb3">
    <label v-if="label" class="mb-2 block text-sm" :for="id">
      {{ label }}
      <span v-if="!required" class="optional-badge text-xs">
        {{ $t('app.optional') }}
      </span>
    </label>

    <editor-content :editor="editor" :class="localTextAreaClasses" />

    <p v-if="help" class="f7 mb3 lh-title">
      {{ help }}
    </p>
  </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';

export default {
  components: {
    EditorContent,
  },

  model: {
    modelValue: {
      type: String,
      default: '',
    },
  },

  props: {
    id: {
      type: String,
      default: 'text-area-',
    },
    type: {
      type: String,
      default: 'text',
    },
    textareaClass: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: '',
    },
    help: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    rows: {
      type: Number,
      default: 3,
    },
    autofocus: {
      type: Boolean,
      default: false,
    },
    maxlength: {
      type: Number,
      default: null,
    },
  },

  emits: ['esc-key-pressed', 'update:modelValue'],

  data() {
    return {
      displayMaxLength: false,
      editor: null,
    };
  },

  watch: {
    modelValue(value) {
      const isSame = this.editor.getHTML() === value;

      if (isSame) {
        return;
      }

      this.editor.commands.setContent(value, false);
    },
  },

  mounted() {
    this.editor = new Editor({
      extensions: [StarterKit],
      editorProps: {
        attributes: {
          class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl m-5 focus:outline-none',
        },
      },
      content: this.modelValue,
      onUpdate: () => {
        // HTML
        this.$emit('update:modelValue', this.editor.getHTML());

        // JSON
        // this.$emit('update:modelValue', this.editor.getJSON())
      },
    });
  },

  beforeUnmount() {
    this.editor.destroy();
  },

  created() {
    this.localTextAreaClasses =
      'p-3 border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm ' +
      this.textareaClass;
  },

  methods: {
    sendEscKey() {
      this.$emit('esc-key-pressed');
    },

    showMaxLength() {
      this.displayMaxLength = true;
    },
  },
};
</script>

<style lang="scss" scoped>
.optional-badge {
  border-radius: 4px;
  color: #283e59;
  background-color: #edf2f9;
  padding: 1px 3px;
}

.length {
  top: 10px;
  right: 10px;
  background-color: #e5eeff;
  padding: 3px 4px;
}

.counter {
  padding-right: 64px;
}
</style>
