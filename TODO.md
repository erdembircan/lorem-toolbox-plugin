# lorem-wordpress-plugin

## 📋 todo-list

- [x] settings page

  - [x] shortcode settings

    - ~~default sentence amount~~
    - [x] min/max sentence length
    - [x] min/max paragraph word length
    - [x] use custom sentences
    - [x] show shortcode override codes for settings

  - ~~auto-generate options~~
    - ~~number of posts~~
    - ~~use more tag~~
    - ~~now use default generation options~~
  - [x] experiment with a frontend library for settings
    - ~~[ ] use react~~
    - [x] use vue
      - [x] vue animation
  - [x] sanitization

- [x] shortcode implementation
  - [x] lorem generation algorithm
- [x] auto generate number of posts
  - [x] use generate algorithm for post title
  - [x] delete posts rights checking
  - [x] improved delete post performance
- [x] front-end stuff
  - [x] vue-mixin for ajax calls
  - [x] error display for ajax calls
  - [x] info component
  - ~~[ ] bundle js with build tools~~
- [x] localization
  - [x] may entirely give up whole localisation thing
  - [x] checkout new localization methods for WordPress
- ~~[ ] admin dashboard info~~
  - number of posts generated
- [x] meta box for shortcode attributes display
  - [x] enable adding lorem from meta box
- [x] auto reload browser for development
  - [x] try out browser-sync
- [x] refactor enque scripts/styles
  - smart algo for differentiating scripts from styles
  - function for getting path and generating version for files
- [x] zip testing
- [x] check suitable names
- [x] readme
  - [readme validator](https://wordpress.org/plugins/developers/readme-validator/)
  - [readme generator](https://generatewp.com/plugin-readme/)
  - [x] checkout correct versions for readme plugin fields
- [x] write an alternative for TweenLite functionality
  - as it turns out, TweenLite's licence is not compatible with GPLv2 so I can't use it for a dependency for an official WordPress plugin.
- [x] homepage for plugin
- [x] submission
  - [x] so far decided on the name `Lorem Toolbox`
    - ~~[ ] change the folder name to match decided name~~
      - for development purposes i decided to not to change the folder name since it won't affect the plugin
  - [general submission guidelines](https://wordpress.org/plugins/developers/#readme)
- [ ] banner image
