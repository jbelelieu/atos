

![ATOS Logo](assets/atos_logo.png)

**Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 .**

Whether you're selling time-based sprints, or simply tracking time worked, ATOS will allow you to manage multiple projects for multiple clients at once, while generating beautiful invoices for you in the process.


💬&nbsp;&nbsp;&nbsp;[Tweet About ATOS](http://twitter.com/intent/tweet?text=Freelancers!+Check+out+ATOS+%2C+a+drop+dead+simple%2C+locally+hosted+story+tracker+and+invoice+generator+designed+for+freelancer+software+developers.&url=https%3A%2F%2Fgithub.com%2Fjbelelieu%2Fato_stories)&nbsp;&nbsp;&nbsp;☕️&nbsp;&nbsp;&nbsp;[Buy me a Coffee!](https://www.buymeacoffee.com/jbelelieu)

ATOS is a locally hosted, zero-setup application that makes invoicing against backlogs drop-dead simple. It does:

- **Project Management**: Track stories against known backlogs (or use it as an independent PM tool)
- **Invoice Generation**: Generate detailed invoices against those completed stories

ATOS is 100% open source and free to use, licensed under the [GNU AGPLv3 License](https://www.gnu.org/licenses/agpl-3.0.en.html).

<img alt="ATOS Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/atos_screen.png?raw=true" style="width: 400px;float:left;" /> <img alt="ATOS Invoice Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/atos_invoice_screen.png?raw=true" style="width: 400px;float:left;" />

-----

- [Setup](#setup)
    - [Download and Start](#download-and-start)
      - [Update Your Default Settings (Optional Step)](#update-your-default-settings-optional-step)
      - [Notice: Updating Your Logo](#notice-updating-your-logo)
      - [Notice: Saving Invoices](#notice-saving-invoices)
      - [Notice: Language Files](#notice-language-files)
      - [Notice: Templates Files](#notice-templates-files)
      - [Notice: PHP 8.1 Requirement](#notice-php-81-requirement)
      - [Notice: phpLiteAdmin](#notice-phpliteadmin)
- [Concepts](#concepts)
- [Features](#features)
- [Tips and Tricks](#tips-and-tricks)
- [FAQ](#faq)
- [Special Thank You!](#special-thank-you)
- [Roadmap](#roadmap)

# Setup

ATOS requires `PHP 8.1+` and `SQLite3`.

**Notice About Deploying ATOS To The Web**: ATOS was always meant to be used locally. While there shouldn't be any problems deploying it, I don't recommend allowing anyone to access it who you don't trust. There is no concept of "users" in the platform, so anyone with access to the platform will be able to do whatever they want with your data.

### Download and Start

- From Github, download the [latest release ZIP file](https://github.com/jbelelieu/atos/releases)
- Unzip it wherever you want on your local machine
- From the command line, go to the ATOS directory and launch the PHP server: `php -S localhost:9001`
- You can now access ATOS from any web browser at `http://localhost:9001`.

#### Update Your Default Settings (Optional Step)

Open `settings.sample.php` and update the values as needed. Optionally rename it to `settings.env.php`, otherwise ATOS will do that for you.

#### Notice: Updating Your Logo

You can add your logo to outgoing invoices by simply replacing `assets/logo.png` in the main directory of the project with your actual logo.

#### Notice: Saving Invoices

If you plan on generating and saving invoices locally (which I recommend you do), you will need to make sure the `invoices` directory is writable: `chmod 0755 invoices`.

Note that invoices are saved as HTML. Most computers have reasonable "Print as PDF" options now; please use that feature to print a PDF if required. Note that ATOS hard codes styles, so changing `assets/style.css` won't affect already saved invoices.

#### Notice: Language Files

You can customize some of the language that isn't on templates using the `includes/language.php` file.

If you happen to translate the application, please share it with the community!

#### Notice: Templates Files

You certainly don't need to, but if you want to, feel free to thinker with all of the templates in the `templates` folder.

Please see the docs for a list of available variables for each template.

If you happen to make a new theme, please share it with the community!

#### Notice: PHP 8.1 Requirement

This does require PHP 8.1+. While I thought about making it backwards compatible, some of the PHP 8.1+ features were too good to pass up. If you need to install PHP 8.1+ (brew should provider the latest version):

- On Mac: `brew update && brew install php && brew link php`
  - To update an existing version of PHP: `brew update && brew upgrade php && brew link --overwrite --force php`

#### Notice: phpLiteAdmin

For your convinience, ATOS ships with [phpLiteAdmin](https://www.phpliteadmin.org/). You can access that from `http://localhost:9001/db`.

ATOS will automatically attempt to run migrations at first start up. On the off chance that migrations fail, you can use phpLiteAdmin to manually execute the contents of `db/migrations.sql`.

-----

# Concepts

- **Company**: every project has a company being billed (client) and a company doing the work (contracted party). In ATOS, the concepts of companies cover both, meaning that you need to add your own company (you are the contracted party), as well as all of your client's company information. Adding both together gives you the flexibility to bill out as separate entities, but keep all of your finances in one place. (See "ATOS landing page")
- **Project**: this is a group of collections (ie stories) that you are billing against. A project can span multiple collections, invoices, etc.. You must have a project to create stories, and you need companies to create a project. (See "ATOS landing page")
- **Collection**: stories are added to collections, and then collections are used to generate invoices. You can think of these as "sprints", but the overall goal of a collection is to combine stories together that are billable over the same period. (Select your project to view and collections)
- **Story**: this is a generic term for any task you completed for a specific project. Stories are lumped into collections, and collections are billed out based on the story statuses within the collection. (Select your project to view and collections)
- **Invoices**: an invoice is the final output of a collection. When you generate an invoice, any story in the story in the collection being invoiced with a status set to "billable" and "complete" will be included and billed according to the story's rate type.
- **Rate Type**: you can bill different amounts based on what you are doing. For example, you might charge $50/hour for one service but $80/hour for another. You set this using "Rate Types". Each rate type is then assigned to a story, and that story is billed out at the appropriate rate. (See "Settings")
- **Story Type**: this is a description way of explaining what kind of story this is. For example, you may want some stories to be coding related, while others are just meetings. In either case, both may be billable, but they are fundamentally different uses of your time. Story types simple help you differentiate between what type of work you did for that story. (See "Settings")
- **Story Status**: this controls the "state" of the story. Each status can be consider "open" or "complete", as well as "billable" or "not billable". For example, you can set a "Rejected" status to be considered "complete" but it won't be billed to the client. (See "Settings")

# Features

- **Drop Dead Simple**: The point of this is to be easy to use, drop dead simple, and efficient. You shouldn't be fighting with project management tools; focus on coding.
- **All Your Projects In One Spot**: Manage as many projects as you wish, each with their own collections, stories, etc..
- **Bill Different Projects As Different Entities**: Set up multiple company profiles, for yourself and your clients. Each project is assigned a contracted party, as well as the client company, allowing you flexiblity to offer services as different entities.
- **Invoicable Story Collections**: Create a collection of stories descrbing one billable period, and automatically generate detailed invoices breaking down your work, the rates for each service, and more.
- **Beautiful Invoices**: Generate beautiful, dynamic, and customizable invoices with one click of the mouse! Everything is template based, allowing you easy access to edit the look and feel of the templates you generate.
- **Story Types**: The application allows you to create as many story types as you wish. Defaults are the standard `Story` and `Chore` types.
- **Rate Types**: Each story can be assigned it's own billable rate. This means that you can offer different hourly rates for different types of services, such as standard coding vs devops rates.
- **Flexible Stories**: Use the story tool to manage your entire project, or copy and paste story IDs and titles directly from your client's JIRA (for example) and use ATOS to track and bill hours against known stories.

# Tips and Tricks

- **Status messages**: Success and error message will appear in the topbar right of the ATOS logo.
- **Last known project navigation**: When you navigate away from a story, a convience link will appear in the top right corner.

# FAQ

**Is this meant to be a replacement for JIRA or Pivotal Tracker?**

While it could very well be, the idea was more that most client projects will already have their own project management tools. The goal here is to track everything you did for the client against their own backlogs, and then bill out accordingly, with references to known ticket IDs.

However, I've also used this quite effectively to manage projects that didn't have a backlog. Do what works best for you!

**What stories get places on invoices?**

All stories set to a "Closed" state. If you don't want something appearing on an invoice, either bump it back to the default collection or into an "Open" state

**Are you planning on modernizes the code?**

I'm aware this isn't the most presentable code, and I promise if the project takes off, I'll clean it up and implement more advanced templating engines, etc..

**Do you plan on making a more dynamic frontend for ATOS?**

While I love Javascript frameworks like React and use them extensively, no, I have no plans of transitioning away from PHP rendering at this time. It's drop dead simple this way, and I want to introduce as little complexity as possible.

**Will future versions remain compatible with the beta?**

100%, no question. I used this personally, and I can't lose years of data for an upgrade. Your data won't becoming obsolete and you won't need to re-import anything with future versions.

**Why isn't this using modern PHP tools like Composer?**

The goal was always a zero-dependency application that can be setup in seconds. By adding complexity in the form of package managers and the such, it adds extra steps I don't want to put people through. In thoery, assuming you have PHP8 installed locally, all you have to do is unzip the latest release and start the PHP server. I intend to keep it that way: simplicity is poetry in code!

# Special Thank You!

- The fine folks over at [phpLiteAdmin](https://www.phpliteadmin.org/) for the SQLite3 manager.
- The artists over at [flaticon](https://flaticon.com/) for their CSS icon set.

# Roadmap

- Editing of companies, projects, rates, types, and statuses
- Story notes
- Project turn-over to-do lists that can be generated and sent to clients much like invoices
- Basic estimated tax help for American freelancers
- Bulk actions on stories
- Expanded language support and language packs
- Various themes
