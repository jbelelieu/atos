![ATOS Logo](assets/screens/atos_logo.png)

**Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 .**

*Now in beta!*

Whether you're selling time-based sprints, or simply tracking time worked, ATOS will allow you to manage multiple projects for multiple clients at once, all while generating beautiful invoices and helping you with your estimated taxes in the process.

💬&nbsp;&nbsp;&nbsp;[Tweet About ATOS](http://twitter.com/intent/tweet?text=Freelancers!+Check+out+ATOS+%2C+a+drop+dead+simple%2C+locally+hosted+story+tracker+and+invoice+generator+designed+for+freelancer+software+developers.&url=https%3A%2F%2Fgithub.com%2Fjbelelieu%2Fato_stories)&nbsp;&nbsp;&nbsp;☕️&nbsp;&nbsp;&nbsp;[Buy me a Coffee!](https://www.buymeacoffee.com/jbelelieu)

**Key Features**

- **Client Management**: Manage all of your clients, as well as companies that you represent.
- **Project Management**: Track stories against known backlogs (or use it as an independent PM tool).
- **Invoice Generation**: Generate detailed invoices against those completed stories.
- **Estimated Taxes**: Estimate your tax burden for the year using customizable tax files for various regions, whether it be at the national (federal), regional (state), or municipal levels (city).
  
**Open Source and Free!**

ATOS is 100% open source and free to use, licensed under the [GNU AGPLv3 License](https://www.gnu.org/licenses/agpl-3.0.en.html).

**Screen Shots**

<img alt="ATOS Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-project-sm.png?raw=true" style="width: 250px;float:left;" /> <img alt="ATOS Invoice Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-invoice-sm.png?raw=true" style="width: 250px;float:left;" /> <img alt="ATOS Invoice Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-taxes-sm.png?raw=true" style="width: 250px;float:left;" />

-----

- [Setup](#setup)
    - [Download and Start](#download-and-start)
      - [Update Your Default Settings (Optional Step)](#update-your-default-settings-optional-step)
      - [Notice: Deploying ATOS To The Web](#notice-deploying-atos-to-the-web)
      - [Notice: Updating Your Logo](#notice-updating-your-logo)
      - [Notice: Saving Invoices and Tax Overviews](#notice-saving-invoices-and-tax-overviews)
      - [Notice: Language Files](#notice-language-files)
      - [Notice: Templates Files](#notice-templates-files)
      - [Notice: PHP 8.1 Requirement](#notice-php-81-requirement)
      - [Notice: phpLiteAdmin](#notice-phpliteadmin)
- [Modules](#modules)
- [Concepts](#concepts)
- [Features](#features)
- [FAQ](#faq)
    - [Is this meant to be a replacement for JIRA or Pivotal Tracker?](#is-this-meant-to-be-a-replacement-for-jira-or-pivotal-tracker)
    - [What tasks get placed on invoices?](#what-tasks-get-placed-on-invoices)
    - [Will future versions remain compatible with the beta?](#will-future-versions-remain-compatible-with-the-beta)
    - [Will future versions require a local PHP Server?](#will-future-versions-require-a-local-php-server)
    - [Can companies use this to management projects?](#can-companies-use-this-to-management-projects)
    - [How long will beta last?](#how-long-will-beta-last)
    - [Why isn't this using modern PHP tools like Composer?](#why-isnt-this-using-modern-php-tools-like-composer)
    - [Are you commiting to maintaining and improving ATOS moving forward?](#are-you-commiting-to-maintaining-and-improving-atos-moving-forward)
    - [How should I handle non-payment of a collection?](#how-should-i-handle-non-payment-of-a-collection)
    - [How do I reconcile what I billed and what I got paid?](#how-do-i-reconcile-what-i-billed-and-what-i-got-paid)
    - [What's the easiest way to import part data?](#whats-the-easiest-way-to-import-part-data)
    - [How do I setup a fixed-rate invoice?](#how-do-i-setup-a-fixed-rate-invoice)
    - [What if I change my rates?](#what-if-i-change-my-rates)
- [Disclaimer](#disclaimer)
    - [ATOS Tax Numbers Are Good-Faith Estimates Only!](#atos-tax-numbers-are-good-faith-estimates-only)
- [Contributing](#contributing)
    - [How to Contribute](#how-to-contribute)
    - [Special Thank You](#special-thank-you)
- [Roadmap](#roadmap)

# Setup

ATOS requires `PHP 8.1+` and `SQLite3`.

### Download and Start

- From Github, download the [latest release ZIP file](https://github.com/jbelelieu/atos/releases)
- Unzip it wherever you want on your local machine
- From the command line, go to the ATOS directory and launch the PHP server: `php -S localhost:9001`
  - If you need to install PHP, please see "Notice: PHP 8.1 Requirement" below for instructions.
  - You can test your machine's version of PHP using `php -v`
- You can now access ATOS from any web browser at `http://localhost:9001`.

#### Update Your Default Settings (Optional Step)

Open `settings.sample.php` and update the values as needed. Optionally rename it to `settings.env.php`, otherwise ATOS will do that for you.

#### Notice: Deploying ATOS To The Web

ATOS was always meant to be used locally. While there shouldn't be any problems deploying it, I don't recommend allowing anyone to access it who you don't trust. There is no concept of "users" in the platform, so anyone with access to the platform will be able to do whatever they want with your data.

#### Notice: Updating Your Logo

You can add your logo to outgoing invoices by simply replacing `assets/logo.png` in the main directory of the project with your actual logo.

#### Notice: Saving Invoices and Tax Overviews

If you plan on generating and saving invoices/taxes locally (which I recommend you do), you will need to make sure the `_generated` directory is writable: `chmod 0755 _generated`.

Invoices/ and taxes files are saved as HTML. Most computers have reasonable `Print as PDF` options now; please use that feature to print a PDF if required. ATOS hard codes styles, so changing `assets/invoiceStyle.css` or `assets/taxStyle.css` won't affect already saved invoices.

#### Notice: Language Files

You can customize some of the language that isn't on templates using the `includes/language.php` file.

If you happen to translate the application, please share it with the community!

#### Notice: Templates Files

You certainly don't need to, but if you want to, feel free to thinker with all of the templates in the `templates` folder.

Please see the docs for a list of available variables for each template.

If you happen to make a new theme, please share it with the community!

#### Notice: PHP 8.1 Requirement

This does require PHP 8.1+. While I thought about making it backwards compatible, some of the PHP 8.1+ features were too good to pass up. If you need to install PHP 8.1+ (brew should provide the latest version):

- On Mac: `brew update && brew install php && brew link php`
  - To update an existing version of PHP: `brew update && brew upgrade php && brew link --overwrite --force php`

#### Notice: phpLiteAdmin

For your convinience, ATOS ships with [phpLiteAdmin](https://www.phpliteadmin.org/). You can access that from `http://localhost:9001/db`.

ATOS will automatically attempt to run migrations at first start up. On the off chance that migrations fail, you can use phpLiteAdmin to manually execute the contents of `db/migrations.sql`.

-----

# Modules

You can find all themes, tax files, and language packs over at the [ATOS Modules](https://github.com/jbelelieu/atos_modules) repo.

-----

# Concepts

These cover anything you can directly interact with/manage using the software:

- **Company**: every project has a company being billed (client) and a company doing the work (contracted party). In ATOS, the concepts of companies cover both, meaning that you need to add your own company (you are the contracted party), as well as all of your client's company information. Adding both together gives you the flexibility to bill out as separate entities, but keep all of your finances in one place. (See "ATOS landing page")
- **Project**: this is a group of collections (ie stories) that you are billing against. A project can span multiple collections, invoices, etc.. You must have a project to create stories, and you need companies to create a project. (See "ATOS landing page")
- **Collection**: stories are added to collections, and then collections are used to generate invoices. You can think of these as "sprints", but the overall goal of a collection is to combine stories together that are billable over the same period. (Select your project to view and collections)
- **Task**: this is a generic term for any task you completed for a specific project. Stories are lumped into collections, and collections are billed out based on the task statuses within the collection. (Select your project to view and collections)
- **Invoices**: an invoice is the final output of a collection. When you generate an invoice, any task in the collection being invoiced with a status set to "billable" and "complete" will be included and billed according to the task's rate type.
- **Rate Type**: you can bill different amounts based on what you are doing. For example, you might charge $50/hour for one service but $80/hour for another. You set this using "Rate Types". Each rate type is then assigned to a task, and that task is billed out at the appropriate rate. (See "Settings")
- **Task Type**: this is a description way of explaining what kind of task this is. For example, you may want some stories to be coding related, while others are just meetings. In either case, both may be billable, but they are fundamentally different uses of your time. Task types simply help you differentiate between what type of work you did for that task. (See "Settings")
- **Task Status**: this controls the "state" of the task. Each status can be consider "open" or "complete", as well as "billable" or "not billable". For example, you can set a "Rejected" status to be considered "complete" but it won't be billed to the client. (See "Settings")
- **Taxes**: Tax year covered by ATOS. Each tax year can have different filing strategies and regions.
- **Tax Deduction**: Any amount that comes directly out of your base income to figure out your taxable income. For example, the US Federal standard deduction.
- **Tax Adjustment**: Any income outside of ATOS that will add to your total taxable income. For example, capital gains taxes of 15% on $5,000 in stock earnings.

# Features

- **Drop Dead Simple**: The point of this is to be easy to use, drop dead simple, and efficient. You shouldn't be fighting with tooling.
- **All Your Projects In One Spot**: Manage as many projects as you wish, each with their own collections, stories, etc..
- **Bill Different Projects As Different Entities**: Set up multiple company profiles, for yourself and your clients. Each project is assigned a contracted party, as well as the client company, allowing you flexiblity to offer services as different entities.
- **Help with your taxes**: create custom strategies (single, married, etc.), set up regions you have tax burdens in (federal, state, city, etc.), input deductions/adjustments, and voila! ATOS will crunch some numbers to estimate how much you'll own and what your estimated payments should be.
- **Invoicable Task Collections**: Create a collection of stories describing one billable period, and automatically generate detailed invoices breaking down your work, the rates for each service, and more.
- **Beautiful Invoices**: Generate beautiful, dynamic, and customizable invoices with one click of the mouse! Everything is template based, allowing you easy access to edit the look and feel of the templates you generate.
- **Task Types**: The application allows you to create as many task types as you wish. Defaults are the standard `Task` and `Chore` types.
- **Rate Types**: Each task can be assigned it's own billable rate. This means that you can offer different hourly rates for different types of services, such as standard coding vs devops rates.
- **Flexible Stories**: Use the task tool to manage your entire project, or copy and paste task IDs and titles directly from your client's JIRA (for example) and use ATOS to track and bill hours against known stories.

# FAQ

### Is this meant to be a replacement for JIRA or Pivotal Tracker?

While it could very well be, the idea was more that most client projects will already have their own project management tools. The goal here is to track everything you did for the client against their own backlogs, and then bill out accordingly, with references to known ticket IDs.

However, I've also used this quite effectively to manage projects that didn't have a backlog. Do what works best for you!

### What tasks get placed on invoices?

The task's status controls whether it appears on invoices, specifically whether you marked it as a "billable" state.

Note that you can have stories with a "complete" status that won't appear in the "Open" state, but at the same time won't appear on invoices. This gives you the flexibility to maintain an accurate backlog (not every task on a project is billable) while still getting paid for billable work!

### Will future versions remain compatible with the beta?

100%, without question. I use this personally, and I can't lose years of data for an upgrade. Your data won't become obsolete and you won't need to re-import anything with future versions.

### Will future versions require a local PHP Server?

While you can certainly continue to use this with a local PHP server, once beta is completed, the plan is to turn this into either a desktop app or a containerized app.

### Can companies use this to management projects?

As of now there isn't a concept of "users", so while in theory it could be used, there wouldn't be any way of limiting who can do what in the application, nor any way of knowning who did what.

Should beta find success, I will be happy to implement more extensive user-based permissions/roles allowing for companies to leverage this more effectively. But again, this was always designed for freelancers, so that was never a consideration going into beta.

### How long will beta last?

Depending on how much feedback I get, I'm hoping to release v1 of ATOS mid-summer. Should the project find good feedback and success, I'll commit to extensive code clean up in line with some of my other modern projects like Zenbership v2.

### Why isn't this using modern PHP tools like Composer?

The goal was always a zero-dependency application that can be setup in seconds. While I could ship with a vendor folder, I want this to be without bloat whenever possible. Sqlite3 allows for easy backups and makes your data highly portable. The lack of a full fledged PHP framekwork makes the package smaller. By adding complexity in the form of package managers and the such, it adds extra steps I don't want to put people through.

In thoery, assuming you have PHP 8.1 installed locally, all you have to do is unzip the latest release and start the PHP server.

### Are you commiting to maintaining and improving ATOS moving forward?

Should ATOS gain a dedicated user-base and receive good feedback during beta, I fully intend to continue working on it. Future work would include a post-beta modernization of the codebase, as well as potential online "cloud-based" versions of the application. But I'll never remove the open source aspect of the application, and it will always be free to use locally.

I personally use this application for all of my freelancing needs, so one way or another development will continue on it.

### How should I handle non-payment of a collection?

If a client doesn't end up paying, you should set the the task to the `Unpaid` status. This will tell the program that you completed the work, but that it was never billed.

### How do I reconcile what I billed and what I got paid?

If a client only paid a fraction of an invoice, you have some options:

- Change the task's hours: you can alter the collection, changing the hours billed to `0`,
- Change your tax burdens: if your goal is more accurate tax reporting, you can add a deduction at for the difference between what you billed and what you were paid. For example, if you were owed were `$10,000` and only got paid `$6,000`, within your tax information for that year, a `$4,000` deduction effectively fixes your actual income since deductions come directly out of your billed amount for the year.

### What's the easiest way to import part data?

While there isn't a CSV importer at this time, you can create collections and bill out the exact amounts that you received against a single task (ie, you don't have to re-enter every task you've ever done). This gives you the benefit of accurate tax data.

See the "fixed-rate invoice" question for more details.

**Important**: Make sure you set the "completed on" date to the correct year the work was done on, otherwise it will be creditted to the current year.

### How do I setup a fixed-rate invoice?

- Create a new rate type with the exact amount of the invoice,
- Create a collection within the project you want to bill a fixed amount for,
- Create a single task with the new rate type,
- Set that task to any billable status

### What if I change my rates?

Always, always, always create a new rate type! Changing existing rates will result in old tax data being invalidated. "Delete" the old rate (it isn't actually deleted, just hidden since we need that for accurate calculations), and create a new rate with the same name + updated hourly.

----

# Disclaimer

### ATOS Tax Numbers Are Good-Faith Estimates Only!

ATOS is not tax software, I am not an accountant, and I don't want to be one.

ATOS is not a substitute for real tax software or the advice of a financial professional.

All tax estimates are good faith estimates, but I can't make any guarantees of accuracy. Please use estimates only as a general guide for your tax obligations.

----

# Contributing

### How to Contribute

Simply create a PR against the `develop` branch of the primary repo or the modules repo. If your contribution is added to the platform, I'll add your name under the "Special Thank You" section below.

Some ways you can contribute include:

- Donate to support additional development
- Build features for the core platform
- Translate the application into a new language or dialect
- Build new report templates
- Create yearly tax files: create one for your regional burdens and share with the world!
- Theming
  - Dashboard themes (no heavy javascript-based solutions)
  - Invoice themes
  - Tax overview themes

### Special Thank You

- The fine folks over at [phpLiteAdmin](https://www.phpliteadmin.org/).
- The artists over at [Icofont](https://icofont.com/).

# Roadmap

- Remove PHP8 requirement in favor of most universally available versions
- Edit companies and project basics
- Task notes and files
- Expanded language support and language packs
- List invoice / tax directory contents
- Better help bubbles, especially for first time users
