
# Alfred.app workflow for Trello.com
[![Code Climate](https://codeclimate.com/github/janhenckens/alfredapp-trello/badges/gpa.svg)](https://codeclimate.com/github/janhenckens/alfredapp-trello)

I created this workflow from a need to quickly open a specific Trello board.

We use a different board per projects, have a lot of projects and thus a lot of boards to jump between all day, so being able to quickyl access a board is a really timesaver.

*Note that using Workflows with Alfred requires that you have a [Powerpack](http://www.alfredapp.com/powerpack/) license.*

#### :point_right: Download the latest version [here](https://github.com/janhenckens/alfredapp-trello/releases/download/v1.3.0/Trello.for.Alfred.alfredworkflow). :point_left:

![Board search example](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_example.png)


## Getting started

Run these command to configure the workflow and start using it:

- **"trello setup"** - will prompt you to give the workflow access to your Trello account. When you confirm this, you'll see a 64 characters long string on the screen. :exclamation: **Copy that string to the clipboard.** :exclamation:

- **"trello save"** - and **paste the key you copied in step 1** and press enter. After a few seconds you'll see a notification and then you're ready to go
---

## Available command
- **"t [boardname]"** - will search all your board names for the string and list all results. Selecting a board and pressing enter will open that board in your browser
- **"t [boardname] me"** - will show you all cards on the board to which you are added/subscribed
- **"t [boardname] [listname]"** - will show you all the cards on that list on that board. (boardnames are converted lowercase and spaces are stripped)
- **"t [boardname] [listname] me"** - will show you all the cards you're subscribed to in that column on that board
- **"t [boarname]-[cardnumber]"** - will take you directly to that card. Every card has a number, find it in the url
- **"ts [boardname] query"** - will search all titles on the given board for the query
- **"ts [boardname] me"** - will show you all cards assigned to you on the given board.

##### More extended example of the commands can be found [here](https://github.com/janhenckens/alfredapp-trello/wiki/available-commands)

---
## Support

The software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.