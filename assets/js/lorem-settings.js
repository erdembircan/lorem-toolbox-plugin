/**
 * capitalize first letter of strings
 * @param {string} val string to be capitalized
 * @returns {string} capitalized string
 */
function cap(val) {
  return val[0].toUpperCase() + val.slice(1);
}

// Vue filter - capitalize only first letter of a single/group of string(s)
Vue.filter('capF', cap);

// Vue filter - capitalize all words of a given string
Vue.filter('capAF', val =>
  val
    .split(' ')
    .map(cap)
    .join(' ')
);

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

Vue.component('generate-posts', {
  template: '#generatePosts',
  props: ['ajaxurl', 'ajaxaction', 'nonce'],
  data() {
    return {
      postCount: 5,
    };
  },
  methods: {
    changeSubmitButtonStatus(status) {
      this.$refs.submitButton.disabled = status;
    },
    handleForm() {
      this.changeSubmitButtonStatus(true);
      return fetch(this.ajaxurl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `nonce=${this.nonce}&post_count=${this.postCount}&action=${this.ajaxaction}`,
      })
        .then(resp => {
          this.changeSubmitButtonStatus(false);
          return resp.json();
        })
        .then(j => console.log(j));
    },
  },
});

// main Vue instance
new Vue({
  data: loremSettings.data,
  components: ['settings-table', 'generate-posts'],
}).$mount('#lorem_app');
