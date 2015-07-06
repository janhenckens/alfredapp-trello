
# Alfred.app workflow for Trello.com
[![Code Climate](https://codeclimate.com/github/janhenckens/alfredapp-trello/badges/gpa.svg)](https://codeclimate.com/github/janhenckens/alfredapp-trello)

I created this workflow from a need to quickly open a specific Trello board. We use a different board per projects, have a lot of projects and thus a lot of boards to jump between all day, so being able to quickyl access a board is a really timesaver.

Note that using Workflows with Alfred requires that you have a [Powerpack](http://www.alfredapp.com/powerpack/) license.


## Getting started

Run these command to configure the workflow and start using it:

- **"trello setup"** - will prompt you to give the workflow access to your Trello account. When you confirm this, you'll see a 64 characters long string on the screen. **Copy that string to the clipboard.**
- **"trello save"** - and **paste the key you copied in step 1** and press enter. After a few seconds you'll see a notification and then you're ready to go

---

## Available command
- **"t [boardname]"** - will search all your board names for the string and list all results. Selecting a board and pressing enter will open that board in your browser
![Board search example](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_example.png)

- **"t [boardname] me"** - will show you all cards on the board to which you are added/subscribed. Alfred limits the results to see here to a maximum of 9 cards.
![Your cards on the board](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_boards_me_example.png)

- **"t [boardname] [listname]"** - will show you all the cards on that list on that board. Note that the boardname is converted to lowercase and stripped of spaces. Searching for "todo" will get you all cards in "To Do".
![Cards](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_cards_example.png)

- **"t [boardname] [listname] me"** - will show you all the cards you're subscribed to in that column on that board.
![My cards](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_cards_me_example.png)

- **"t [boarname]-[cardnumber]"** - will take you directly to that card. Trello gives each card a sequential number and if you use and/or know the number of the card, this is an easy way to quickly get to a specific card on a specific board.
![Cards](https://raw.githubusercontent.com/janhenckens/alfredapp-trello/gh-pages/assets/alfred_trello_tickets_example.png)


## Support

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.