![ATOS Logo](assets/screens/atos_logo.png)

**Built by freelancer 🙋‍♂️, for freelancer 🕺 🤷 💃🏾 .**

*Now in beta!* --- ATOS is every freelancer's one-stop shop for managing clients, projects, and your taxes. Whether you're selling time-based sprints, or simply tracking time worked, ATOS allows you to manage multiple projects for multiple clients at once, all while generating beautiful invoices and helping you with your estimated taxes in the process.

- 📔&nbsp;&nbsp;&nbsp;[Documentation & Installation](https://jbelelieu.github.io/atos/)
- 💬&nbsp;&nbsp;&nbsp;<a href="http://twitter.com/intent/tweet?text=Freelancers!%20Check%20out%20ATOS,%20free%20software%20designed%20to%20help%20you%20manage%20your%20clients,%20invoices,%20and%20estimated%20taxes.%20https://github.com/jbelelieu/atos" target="_blank">Tweet About ATOS</a>
- 🎥&nbsp;&nbsp;&nbsp;<a href="https://youtu.be/DY_ze39ZRt8" target="_blank">Walkthrough Video</a>
- 🤑&nbsp;&nbsp;&nbsp;<a href="https://opencollective.com/castlamp/projects/by_freelancer_for_freelancer/" target="_blank">Support Me Financially</a>
- ☕️&nbsp;&nbsp;&nbsp;<a href="https://www.buymeacoffee.com/jbelelieu" target="_blank">Buy Me A Coffee</a>

>

**Key Features**

- **Client Management**: Manage your clients, as well as companies that you represent, all from the same dashboard. Assign different contracting parties and clients for each project.
- **Project Management**: Track tasks against known backlogs, or use ATOS as a full-fledged independent project management tool.
- **Task Management**: Group tasks into collections and bill out against billable tasks within collections.
- **Invoices**: Generate detailed invoices against those completed tasks.
- **Estimated Taxes**: Estimate your tax burden for the year using customizable tax files for various regions, whether it be at the national (federal), regional (state), or municipal levels (city).
- **Reports**: Generate reports for your clients, filtered by project type/status/etc... Need more flexibility? Create your own report templates and [contribute](#contributing) to the community.

**Open Source and Free!**

ATOS is 100% open source and free to use, licensed under the [GNU AGPLv3 License](https://www.gnu.org/licenses/agpl-3.0.en.html).

**Screen Shots**

<center>Project Overview Screen</center>
<img alt="Projects" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-project-sm.png?raw=true" />
<br />
<center>Invoice</center>
<img alt="Invoice" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-invoice-sm.png?raw=true" />
<br />
<center>Tax Estimate</center>
<img alt="Tax Estimate" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-taxes-sm.png?raw=true" />
<br />
<center>Tax Overview</center>
<img alt="Tax Overview" src="https://github.com/jbelelieu/atos/blob/develop/assets/screens/atos-screen-tax-sm.png?raw=true" />

-----

- [Setup & Installation](#setup--installation)
  - [If You Have PHP7 Installed Locally](#if-you-have-php7-installed-locally)
- [Contributing](#contributing)
  - [How to Contribute](#how-to-contribute)
  - [Special Thanks](#special-thanks)
- [Roadmap](#roadmap)

----

# Setup & Installation

ATOS requires `PHP7+` and `SQLite3`.

## If You Have PHP7 Installed Locally

- From Github, download the [latest release ZIP file](https://github.com/jbelelieu/atos/releases)
- Unzip it to your local machine into a writable folder
- From the command line, go to the ATOS directory and launch the PHP server: `php -S localhost:9001`
- You can now access ATOS from any web browser at `http://localhost:9001`.

Please see the [documentation](docs/index.md) if you need a more detailed installation walkthrough.

# Contributing

## How to Contribute

- **Donate**: Donate to support additional development
- **Develop**: Build features for the core platform
- **Translate**: Translate the application into a new language
- **Report Templates**: Build new report templates
- **Tax Files**: create one for your regional burdens and share with the world!
- **Themes**:
  - Dashboard themes (no heavy javascript-based solutions)
  - Invoice themes
  - Tax estimation themes

## Special Thanks

- The folks over at [phpLiteAdmin](https://www.phpliteadmin.org/).
- The artists over at [Icofont](https://icofont.com/).

----

# Roadmap

- Edit companies and project basics
- Task notes and files
- Expanded language support and language packs
- File and data syncing via a cloud service (dropbox, etc.)
- Better help bubbles, especially for first-time users
- Cloud-based option for freelancers on the move!
  