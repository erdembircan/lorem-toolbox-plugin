# 🐛 bug report

- [x] [**01**] shortcode atts only working for `p` (paragraph) attribute.
  - it seems like `WordPress` auto lowercase shortcode attributes with a filter. changing attributes' names to lowercase resolved the problem.
 
- [ ] [**02**] since bulk creating all posts in seconds, posts' `date` attributes are all the same. this may be problematic in testing various theme functionality (getting next/previous posts). experiment on defining different date values on post creation.
