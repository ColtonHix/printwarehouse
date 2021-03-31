Colton Hix
CS 401

Print Warehouse
-----

# Important things to note:

## User credentials (to the site, not github or anything):
 - Username: admin
 - Password: admin

-----

 - This is the only user listed as an admin/creator. If you want to create products, accept requests or view all requests you need to be logged in as that.
 - The site does not yet have a way to create creator/admin accounts, they must be created manually. This is to reinforce the idea that I am the supreme ruler of the site, and have complete control of who gets to create products...
 - Also because I wanted to see if sending emails through the site was in a future homework, but definitely the first reason mostly...

 <sup><sub>That was a joke&trade;</sup></sub>

 - For some reason, Heroku likes to delete product images when I push, or when it spins off. I don't know if this is from my gitignore, heroku itself, or what. Text data stored in the database is staying and retrieving as expected, but images are just being removed for some reason. Ideally images are stored on a third party like cloudfront but I did not set that up for this homework assignment.
    - Creating fresh products will retain their images (until I push)
    - I probably just didn't configure my gitignore correctly, but I'd rather not push 30 random test images to Heroku every time I push hence the gitignore so I'll let you create your own :)

## Create your forms requirement
- I may have forgot about/cut some forms but I believe most are there
- There should be at least one place where you can create, update, delete and retreive data
 - I did not implement search in this homework, it may come later

## PHP data retrieval and sanitization requirement
- Technically most pages use AJAX calls to send and retreive data
- In the end, this hits the PHP API (more on that later) so it still uses PHP

## Validate form data requirement
 - Most validation is JS validation on front end
 - Just about everything is an AJAX call so form inputs won't clear unless told to
 - Bootstrap forms provide very obvious alerts for incorrect fields
 - Things that need it also have backend validation (exists, match pattern, etc.)
    - Pretty much the only things with heavier backend validation other than "does the value exist" are prices, images and usernames
 - I did not enforce every single field to have a pattern or length requirement. Things that have special patterns are always verified on the backend aswell (prices, emails, usernames, some other things)
 - Register's username uses a regualar expression for validation. There are also some other, smaller ones scattered throughout the site for various purposes other than validation.

## Session requirement
 - Many of the pages on this site will enforce a user to be logged in via session vars and display a 404 otherwise
 - About half the links on the sitemap will display a 404 if you aren't logged in; a few of them require you are a creator/admin

## Save form data to MySQL requirement
**SQL access things are not in the DAO object**

Only the connection is

- I took the approach of emulating a restful API with htaccess modifications
    - definitely not the best way to make an API, but it worked for this project
    - You can find the SQL access methods in the Handlers
    - All SQL statements are stored procedures. Stored procedures can be found in the sql directory.
    - I did this for the following reasons:
        - API's are what I'm used to. To me, they are more portable across pages, resemble object orientation & interfacing, easier to clean input/output, etc.
        - More organization!

## Get data from MySQL to display
 - All the methods in the Handlers use PDO prepared statements
 - Those statements simply call stored procedures
 - There is at least one INSERT, UPDATE, and DELETE somewhere on the site. I believe UPDATE is the only singleton (edit product)
 - Many of the queries will only let specific users view data; you will need to swap between the user and admin acccount to see all existing site functionality
 - Again, displaying is done mostly by JavaScript (AJAX calls and handlers)

## Login requirement
 - There is a link in the login modal to create a new basic user account
 - Currently there is no way to provision admin/creator accounts, I created them manually
    - Again, this is because I would prefer if these used email verification which I did not rush to do since it wasn't in the description.

## Miscellanious notes
 - I am aware the search bar can make the header display weird and does nothing. I'm not sure how I want to handle searching & sorting yet, so I left it there as a placeholder.
 - Product and Request lists are not paginated (yet...) they will display everything in a big list
 - The site isn't fully size responsive. There are a few scaling areas in between the bootstrap resize breakpoints where you enter the twilight zone and things may get funky. Site should look fine for most screen sizes though.
 - ClearDB can be a bit slow sometimes; it may take a second or two for a request to go through
    - Eventually I'll make the buttons have a spinner or say loading, but for now they just disable themselves
 - I did not add the front page slider/featured products area. I plan to add that when the site is paginated so the user doesn't see 2 of the same product on the page all the time.
 - My ClearDB is hardcoded to insert requests as Boise timezone. I wanted to have requests say 'created x hours/days/weeks/etc. ago' instead of a raw date. This works great for users in the Boise timezone, but I have no idea how this works for users outside of it. JS Date objects act pretty funky sometimes (<sup><sub>they suck</sub></sup>), so in the future I'll either convert timezones or just display a raw date.
