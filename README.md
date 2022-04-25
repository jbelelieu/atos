# ATOS

ATOS is a locally hosted, no setup-required, application that makes invoicing against backlogs drop dead simple. ATOS is designed to:

- Track stories
- Generate invoices against those completed stories

ATOS is 100% open source and free to use.

💬&nbsp;&nbsp;&nbsp;[Spread the word on Twitter!](http://twitter.com/intent/tweet?text=Freelancers!+Check+out+ATO+Stories+%2C+a+drop+dead+simple%2C+locally+hosted+story+tracker+and+invoice+generator+designed+for+freelancer+software+developers.&url=https%3A%2F%2Fgithub.com%2Fjbelelieu%2Fato_stories)

☕️&nbsp;&nbsp;&nbsp;[Buy me a Coffee!](https://www.buymeacoffee.com/jbelelieu)

-----

# Notice About Deploying ATOS To The Web

This was always meant to be used locally. While there shouldn't be any problems deploying it, I don't recommend allowing anyone to access this who you don't fully trust. There is no concept of "users" in the platform, so anyone with access will be able to do whatever they want with your data.

-----

# Requirements

- PHP8+
- SQLite3

# Local Setup

## Create the SQLite Database

Create an empty SQLite database fike inside the `db` folder named `atos.sqlite3`.

## Start the PHP Server

```
php -S localhost:9001
```

You can now access ATOS from any web browser at `http://localhost:9001`.

## Run the Base Migrations Against the New Database

You can find the migrations at `db/migrations.sql`.

For your convinience, ATOS ships with [phpLiteAdmin](https://www.phpliteadmin.org/). You can access that from `http://localhost:9001/db`.

## Invoices

If you plan on generating and saving invoices locally, you will need to make sure the `invoices` directory is writable: `chmod 0755 invoices`

-----

# Features

- **Drop Dead Simple**: The point of this is to be easy to use, drop dead simple, and efficient. You shouldn't be fighting with project management tools; focus on coding.
- **All Your Projects In One Spot**: Manage as many projects as you wish, each with their own collections, stories, etc..
- **Bill Different Projects As Different Entities**: Set up multiple company profiles, for yourself and your clients. Each project is assigned a contracted party, as well as the client company, allowing you flexiblity to offer services as different entities.
- **Invoicable Story Collections**: Create a collection of stories descrbing one billable period, and automatically generate detailed invoices breaking down your work, the rates for each service, and more.
- **Beautiful Invoices**: Generate beautiful, dynamic, and customizable invoices with one click of the mouse! Everything is template based, allowing you easy access to edit the look and feel of the templates you generate.
- **Story Types**: The application allows you to create as many story types as you wish. Defaults are the standard `Story` and `Chore` types.
- **Rate Types**: Each story can be assigned it's own billable rate. This means that you can offer different hourly rates for different types of services, such as standard coding vs devops rates.
- **Flexible Stories**: Use the story tool to manage your entire project, or copy and paste story IDs and titles directly from your client's JIRA (for example) and use ATOS to track and bill hours against known stories.

-----

# Tips and Tricks

## Example Use Case

Mine! I built this because I was juggling multiple contract projects at once and needed to keep track of what I was working on, billing, etc.. This pseudo-PM tool with built in invoice generation allows me to manage all projects for multiple clients all at once, without needing a bunch of different software.
