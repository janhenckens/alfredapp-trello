# Alfred.app workflow for Trello.com

## Getting started

Run these command to configure the workflow and start using it:

- "trello setup" - will prompt you to give the workflow access to your Trello account. When you confirm this, you'll see a 64 characters long string on the screen. **Copy that string to the clipboard.**
-  "trello save" - and **paste the key you copied in step 1** and press enter. After a few seconds you'll see a notification and then you're ready to go

## Available command
- **"t [boardname]"** - will search all your board names for the string and list all results. Selecting a board and pressing enter will open that board in your browser
- **"t [boardname] [listname]"** - will show you all the cards you're subscribed to on that list on the board. Note that the boardname is converted to lowercase and stripped of spaces. Searching for "todo" will get you all cards in "To Do".