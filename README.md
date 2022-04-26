![ATOS Logo](assets/atos_logo.png)

**Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 .**

Whether you're selling time-based sprints, or simply tracking time worked, ATOS will allow you to manage multiple projects for multiple clients at once, while generating beautiful invoices for you in the process.


💬&nbsp;&nbsp;&nbsp;[Tweet About ATOS](http://twitter.com/intent/tweet?text=Freelancers!+Check+out+ATOS+%2C+a+drop+dead+simple%2C+locally+hosted+story+tracker+and+invoice+generator+designed+for+freelancer+software+developers.&url=https%3A%2F%2Fgithub.com%2Fjbelelieu%2Fato_stories)&nbsp;&nbsp;&nbsp;☕️&nbsp;&nbsp;&nbsp;[Buy me a Coffee!](https://www.buymeacoffee.com/jbelelieu)

ATOS is a locally hosted, zero-setup application that makes invoicing against backlogs drop-dead simple. It does:

- **Project Management**: Track stories
- **Invoice Generation**: Generate detailed invoices against those completed stories
- **Estimated Taxes**: Coming soon -- Estimated tax help for US-based developers.
- **Language Support**: Coming soon  -- additional languages other than English.

ATOS is 100% open source and free to use, licensed under the [GNU AGPLv3 License](https://www.gnu.org/licenses/agpl-3.0.en.html).

<img alt="ATOS Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/atos_screen.png?raw=true" style="width: 400px;float:left;" /> <img alt="ATOS Invoice Screen Shot" src="https://github.com/jbelelieu/atos/blob/develop/assets/atos_invoice_screen.png?raw=true" style="width: 400px;float:left;" />

-----

# Setup

**Notice About Deploying ATOS To The Web**: ATOS was always meant to be used locally. While there shouldn't be any problems deploying it, I don't recommend allowing anyone to access this who you don't fully trust. There is no concept of "users" in the platform, so anyone with access will be able to do whatever they want with your data.

ATOS requires `PHP8+` and `SQLite3`.

### Download and Start

- From Github, download the ZIP file for the latest release
- Unzip it wherever you want
- In command line, go to that directory and launch the PHP server: `php -S localhost:9001`
- You can now access ATOS from any web browser at `http://localhost:9001`.

##### Update Your Default Settings (Optional Step)

Open `settings.sample.php` and update the values as needed. Optionally rename it to `settings.env.php`, otherwise ATOS will do that for you.

##### Notice: phpLiteAdmin

For your convinience, ATOS ships with [phpLiteAdmin](https://www.phpliteadmin.org/). You can access that from `http://localhost:9001/db`.

ATOS will automatically attempt to run migrations at first start up. On the off chance that migrations fail, you can use phpLiteAdmin to manually execute the contents of `db/migrations.sql`.

##### Notice: Updating Your Logo

You can add your logo to outgoing invoices by simply replacing `assets/logo.png` in the main directory of the project with your actual logo.

##### Notice: Saving Invoices

If you plan on generating and saving invoices locally (which I recommend you do), you will need to make sure the `invoices` directory is writable: `chmod 0755 invoices`.

Note that invoices are saved as HTML. Most computers have reasonable "Print as PDF" options now; please use that feature to print a PDF if required. Note that ATOS hard codes styles, so changing `assets/style.css` won't affect already saved invoices.

##### Notice: Language Files

You can customize some of the language that isn't on templates using the `includes/language.php` file.

If you happen to translate the application, please share it with the community!

##### Notice: Templates Files

You certainly don't need to, but if you want to, feel free to thinker with all of the templates in the `templates` folder.

Please see the docs for a list of available variables for each template.

If you happen to make a new theme, please share it with the community!

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

# FAQ

**What stories get places on invoices?**
All stories set to a "Closed" state. If you don't want something appearing on an invoice, either bump it back to the default collection or into an "Open" state

**Are you planning on modernizes the code?**
I'm aware this isn't the most presentable code, and I promise if the project takes off, I'll clean it up and implement more advanced templating engines, etc..

**Do you plan on making a more dynamic frontend for ATOS?**
While I love Javascript frameworks like React and use them extensively, no, I have no plans of transitioning away from PHP rendering at this time. It's drop dead simple this way, and I want to introduce as little complexity as possible.

**Will future versions remain compatible with the beta?**
100%, no question. I used this personally, and I can't lose years of data for an upgrade. Your data won't becoming obsolete and you won't need to re-import anything with future versions.

# Special Thank Yous

- The fine folks over at [phpLiteAdmin](https://www.phpliteadmin.org/) for the SQLite3 manager.
- The artists over at [flaticon](https://flaticon.com/) for their CSS icon set.