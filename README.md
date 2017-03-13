This package helps to create hangouts groups and monitor their members

### How to install?
    composer require lisand7ru/hangoutsgroups
    
### Creating a group
    $groupsManager = new GroupsManger('my_cookies');
    $group = $groupsManager->create();
    
### Monitoring
    $link = $group->getLink(); //returns a link on your group
    $members = $group->getMembers(); //returns an array of members (id, name, picture)
    
### Where to get the cookies?
* visit hangouts.google.com and auth there
* via Developers Tools take a value of header param "Cookies:" and pass it to the GroupsManager's controller