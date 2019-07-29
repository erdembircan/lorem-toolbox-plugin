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

Vue.use(VueResource);
Vue.http.options.emulateJSON = true;
Vue.http.options.emulateHTTP = true;

// mixins
Vue.mixin({
  computed: {
    resource() {
      return this.$resource(this.ajaxurl);
    },
  },
});

loremSettings.data.tweenedNumber = loremSettings.data.post_count;

// main Vue instance
new Vue({
  data: loremSettings.data,
  components: ['settings-table', 'generate-posts', 'status'],
  methods: {
    updatePostCount(data) {
      if (data) {
        this.post_count = data.totalCount;
      }
    },
  },
  computed: {
    animatedCount() {
      return parseInt(this.tweenedNumber, 10).toFixed(0);
    },
  },
  watch: {
    post_count(newVal) {
      TweenLite.to(this.$data, 1, { tweenedNumber: newVal });
    },
  },
}).$mount('#lorem_app');
