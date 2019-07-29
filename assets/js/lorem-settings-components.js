// status component
Vue.component('status', {
  props: ['fetching', 'data'],
  template: '#loremStatus',
  computed: {
    statusColor() {
      return this.data.type === 'ok' ? 'green' : 'red';
    },
  },
});

// setting-row component
Vue.component('setting-row', {
  props: ['element', 'domProps'],
  render(h) {
    const options = {
      domProps: this.domProps,
    };
    return h(this.element, options);
  },
});

// settings-table component
Vue.component('settings-table', {
  template: '#loremSettingsTable',
  props: ['settings'],
});

// generate-posts component
Vue.component('generate-posts', {
  template: '#generatePosts',
  props: ['ajaxurl', 'ajaxactiongenerate', 'nonce', 'ajaxactiondelete'],
  data() {
    return {
      postCount: 5,
      fetching: false,
      messageData: {
        message: '',
        type: 'ok',
      },
    };
  },
  watch: {
    fetching(status) {
      this.$refs.submitButton.disabled = status;
      this.$refs.deleteButton.disabled = status;
    },
  },
  methods: {
    handleForm() {
      this.fetching = true;
      this.resource
        .save({ action: this.ajaxactiongenerate, nonce: this.nonce, post_count: this.postCount })
        .then(({ body }) => {
          this.fetching = false;
          if (body.error) {
            throw new Error(body.error);
          }
          this.handleStatus({ message: 'posts generated successfully', type: 'ok' });
          this.$emit('fetched', body.data);
        })
        .catch(err => {
          this.fetching = false;
          this.handleStatus({ message: err.message, type: 'error' });
        });
    },
    deleteGenerated() {
      this.fetching = true;
      this.resource
        .get({ nonce: this.nonce, action: this.ajaxactiondelete })
        .then(({ body }) => {
          this.fetching = false;
          if (body.error) {
            throw new Error(body.error);
          }
          this.handleStatus({ message: 'posts deleted successfully', type: 'ok' });
          this.$emit('fetched', body.data);
        })
        .catch(err => {
          this.fetching = false;
          this.handleStatus({ message: err.message, type: 'error' });
        });
    },
    handleStatus(mD) {
      this.messageData = mD;
    },
  },
});
