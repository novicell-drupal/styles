# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* Quick summary
* Version
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Summary of set up
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact

## Color-theme preview styling

Color-theme previews, including the empty option, use equal 35px squares with
no border. Selection uses an offset outline, avoiding a size/layout change.
The optional `--styles-preview-focus-color` CSS property customizes the selection
outline; it falls back to the existing blue without an admin-theme dependency.

Theme palette values stay with the frontend theme. Gin LB class normalization
and the specificity needed inside Gin dialogs belong in `novicell/premium_gin`,
not this module. Existing non-color preview dimensions and button behavior are
unchanged.

Verify selected/unselected and empty color previews in section and block forms,
alongside another preview type. This module has no Gin dependency and continues
to work on other admin themes. Feature branch base: `1.x` (no `main` branch).
