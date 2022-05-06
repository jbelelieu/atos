# Documentation

## User Documentation

- [Companies](companies.md)
- [Projects, Collections and Tasks](projects.md)
- [Taxes](taxes.md)
- [Reports](reports.md)
- [Modules](https://github.com/jbelelieu/atos_modules)
- [FAQ](faq.md)

## Technical Documentation

- [Template Objects](objects.md): see what variables are available to you on various templates.

---

# Walkthrough Video

[Click here](https://youtu.be/DY_ze39ZRt8) to view the walkthrough video, which goes over setting up and using the application.

----

# On This Page

- [Documentation](#documentation)
  - [User Documentation](#user-documentation)
  - [Technical Documentation](#technical-documentation)
- [Walkthrough Video](#walkthrough-video)
- [On This Page](#on-this-page)
- [Setup & Installation](#setup--installation)
  - [If You Have PHP7 Installed Locally](#if-you-have-php7-installed-locally)
  - [If You Don't Have PHP7 Installed](#if-you-dont-have-php7-installed)
- [Update Your Default Settings (Optional Step)](#update-your-default-settings-optional-step)
  - [Important Notices](#important-notices)
    - [Deploying ATOS To The Web](#deploying-atos-to-the-web)
    - [Optimized Viewing Experience (Sorry, No Mobile)](#optimized-viewing-experience-sorry-no-mobile)
    - [Updating Your Logo](#updating-your-logo)
    - [Saving Invoices, Reports, and Tax Documents](#saving-invoices-reports-and-tax-documents)
    - [Language Files](#language-files)
    - [Templates Files](#templates-files)
    - [phpLiteAdmin](#phpliteadmin)
- [Core Concepts](#core-concepts)

# Setup & Installation

ATOS requires `PHP7+` and `SQLite3`.

## If You Have PHP7 Installed Locally

- From Github, download the [latest release ZIP file](https://github.com/jbelelieu/atos/releases)
- Unzip it to your local machine into a writable folder
- From the command line, go to the ATOS directory and launch the PHP server: `php -S localhost:9001`
- You can now access ATOS from any web browser at `http://localhost:9001`.

## If You Don't Have PHP7 Installed

Do the following then follow the above directions:

- On Mac: `brew update && brew install php && brew link php`
  - To update an existing version of PHP: `brew update && brew upgrade php && brew link --overwrite --force php`
- You can test your machine's version of PHP from teh command line using `php -v`

# Update Your Default Settings (Optional Step)

Open `settings.sample.php` and update the values as needed. Optionally rename it to `settings.env.php`, otherwise ATOS will do that for you.

## Important Notices

### Deploying ATOS To The Web

ATOS was always meant to be used locally. While there shouldn't be any problems deploying it, I don't recommend allowing anyone to access it who you don't trust. There is no concept of "users" in the platform, so anyone with access to the platform will be able to do whatever they want with your data.

### Optimized Viewing Experience (Sorry, No Mobile)

This software was never optimized for mobile; in fact, it was intended to be used with relatively large resolution displays.

### Updating Your Logo

You can add your logo to outgoing invoices by simply replacing `assets/logo.png` in the main directory of the project with your actual logo.

### Saving Invoices, Reports, and Tax Documents

If you plan on generating and saving generated documents locally (which I recommend you do), you will need to make sure the `_generated` and `_vault` directories are writable: `chmod 0755 _generated && chmod 0755 _vault`.

Invoices/ and taxes files are saved as HTML. Most computers have reasonable `Print as PDF` options now; please use that feature to print a PDF if required. ATOS hard codes styles, so changing `assets/invoiceStyle.css` or `assets/taxStyle.css` won't affect already saved invoices.

### Language Files

You can customize some of the language that isn't on templates using the `includes/language.php` file.

If you happen to translate the application, please share it with the community!

### Templates Files

You certainly don't need to, but if you want to, feel free to thinker with all of the templates in the `templates` folder.

Please see the docs for a list of available variables for each template.

If you happen to make a new theme, please share it with the community!

### phpLiteAdmin

For your convinience, ATOS ships with [phpLiteAdmin](https://www.phpliteadmin.org/). You can access that from `http://localhost:9001/db`.

ATOS will automatically attempt to run migrations at first start up. On the off chance that migrations fail, you can use phpLiteAdmin to manually execute the contents of `db/migrations.sql`.

---


# Core Concepts

These cover anything you can directly interact with/manage using the software:

- **Company**: every project has a company being billed (client) and a company doing the work (contracted party). In ATOS, the concepts of companies cover both, meaning that you need to add your own company (you are the contracted party), as well as all of your client's company information. Adding both together gives you the flexibility to bill out as separate entities, but keep all of your finances in one place. (See "ATOS landing page")
- **Documents**: the application allows you to generate and save things like invoices, reports, and tax estimations. Documents are where you can find all of the generated/saved items.
- **Project**: this is a group of collections (ie stories) that you are billing against. A project can span multiple collections, invoices, etc.. You must have a project to create stories, and you need companies to create a project. (See "ATOS landing page")
  - **Project Link**: A link associated with a project.
  - **Project File**: An uploaded file associated with a project.
- **Collection**: stories are added to collections, and then collections are used to generate invoices. You can think of these as "sprints", but the overall goal of a collection is to combine stories together that are billable over the same period. (Select your project to view and collections)
- **Task**: this is a generic term for any task you completed for a specific project. Stories are lumped into collections, and collections are billed out based on the task statuses within the collection. (Select your project to view and collections)
  - **Rate Type**: you can bill different amounts based on what you are doing. For example, you might charge $50/hour for one service but $80/hour for another. You set this using "Rate Types". Each rate type is then assigned to a task, and that task is billed out at the appropriate rate. (See "Settings")
  - **Type**: this is a description way of explaining what kind of task this is. For example, you may want some stories to be coding related, while others are just meetings. In either case, both may be billable, but they are fundamentally different uses of your time. Task types simply help you differentiate between what type of work you did for that task. (See "Settings")
  - **Status**: this controls the "state" of the task. Each status can be consider "open" or "complete", as well as "billable" or "not billable". For example, you can set a "Rejected" status to be considered "complete" but it won't be billed to the client. (See "Settings")
- **Invoices**: an invoice is the final output of a collection. When you generate an invoice, any task in the collection being invoiced with a status set to "billable" and "complete" will be included and billed according to the task's rate type.
- **Taxes**: Tax year covered by ATOS. Each tax year can have different filing strategies and regions.
  - **Money Set Aside**: During the months between estimated tax payments being due, this allow you to track where you are keeping that money. For example, your savings account or a stock.
  - **Tax Deduction**: Any amount that comes directly out of your base income to figure out your taxable income. For example, the US Federal standard deduction.
  - **Tax Adjustment**: Any income outside of ATOS that will add to your total taxable income. For example, capital gains taxes of 15% on $5,000 in stock earnings.
