TranStringOid
=============

A php based web app which let you translate strings.xml from your android app.
Choose a project name, import your strings.xml files, select desired languages and you're good to go.

### Requirements

* Apache server
* PHP 5.3 and above

### Installation

1. Download or clone the project
2. Edit appFolder/includes/Config/config.ini according to your environement
3. Make appFolder/Projects/ Writable

### Usage

To edit a translation click on the desired project. To get your translated string.xml , in the home page, click on a project then select the desired file.

To delete a project simply delete the folder in appFolder/projects/ .

When translating  : an empty translation will be set to the same string than the original file.

### Screenshot

Translations management : 

![translationmanage](http://oroger.fr/transtringoid/trans01.png)

Translate a project : 

![translationmanage](http://oroger.fr/transtringoid/trans02.png)

### Based on

* [Pry framework](https://github.com/grunk/Pry) for a quick mvc and some usefull classes
* [Twitter bootstrap] (http://twitter.github.io/bootstrap/) for the front end.

### Warning

This project is not (at least for the moment) intended to be hosted on public server. Meaning that security is probably not perfect and there is no control access.

### Limitation

Only handle simple strings file which looks like : 
```
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <string name="key">Value</string>
</resources>
```

Extras attributes are currently not handled (feel free to add it :) ).

Original file and translated one will have the same structure.

High number of translations will probably break the ui.

Probably a truckload of sneaky bugs ...
