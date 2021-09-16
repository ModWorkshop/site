## The folder strucutre

Right now the components folder contains 4 special folders:

* `common` Contains common components, these are not exactly controls and are more specific to our site. For example mod, user, etc.
* `core` Core elements that *aren't* specific to our site, for example a button, a markdown editor, tabs.
* `layout` Components for the layout of the site. Like header and footer, flexbox.
* `pages` A folder that contains components specific to pages.

## Motivation

As the project gets bigger, the components folder will grow huge making it annoying to navigate through. Separating components based on common idea is a good idea. `core` and `layout` might be a little confusing, but if something is used solely for the purpose of layout then it is layout. A tab or dropdown menu for example *might* be considered layout, but it's more of a control element.

## Is this final?
Big fat no. I tried searching for recommended folder structures, but most aren't really considering an actual big project containing 100s of components, we wouldn't want to dump all components to the folder without any separation.