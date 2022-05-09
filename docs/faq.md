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


# Is this meant to be a replacement for JIRA or Pivotal Tracker?

While it could very well be, the idea was more that most client projects will already have their own project management tools. The goal here is to track everything you did for the client against their own backlogs, and then bill out accordingly, with references to known ticket IDs.

However, I've also used this quite effectively to manage projects that didn't have a backlog. Do what works best for you!

# What tasks get placed on invoices?

The task's status controls whether it appears on invoices, specifically whether you marked it as a "billable" state.

Note that you can have tasks with a "complete" status that won't appear in the "Open" state, but at the same time won't appear on invoices. This gives you the flexibility to maintain an accurate backlog (not every task on a project is billable) while still getting paid for billable work!

# Will future versions remain compatible with the beta?

100%, without question. I use this personally, and I can't lose years of data for an upgrade. Your data won't become obsolete and you won't need to re-import anything with future versions.

# Will future versions require a local PHP Server?

While you can certainly continue to use this with a local PHP server, once beta is completed, the plan is to turn this into either a desktop app or a containerized app.

# Can companies use this to management projects?

As of now there isn't a concept of "users", so while in theory it could be used, there wouldn't be any way of limiting who can do what in the application, nor any way of knowning who did what.

Should beta find success, I will be happy to implement more extensive user-based permissions/roles allowing for companies to leverage this more effectively. But again, this was always designed for freelancers, so that was never a consideration going into beta.

# How long will beta last?

Depending on how much feedback I get, I'm hoping to release v1 of ATOS mid-summer. Should the project find good feedback and success, I'll commit to extensive code clean up in line with some of my other modern projects like Zenbership v2.

# Why isn't this using modern PHP tools like Composer?

The goal was always a zero-dependency application that can be setup in seconds. While I could ship with a vendor folder, I want this to be without bloat whenever possible. Sqlite3 allows for easy backups and makes your data highly portable. The lack of a full fledged PHP framekwork makes the package smaller. By adding complexity in the form of package managers and the such, it adds extra steps I don't want to put people through.

In thoery, assuming you have PHP 8.1 installed locally, all you have to do is unzip the latest release and start the PHP server.

# Are you commiting to maintaining and improving ATOS moving forward?

Should ATOS gain a dedicated user-base and receive good feedback during beta, I fully intend to continue working on it. Future work would include a post-beta modernization of the codebase, as well as potential online "cloud-based" versions of the application. But I'll never remove the open source aspect of the application, and it will always be free to use locally.

I personally use this application for all of my freelancing needs, so one way or another development will continue on it.

# How should I handle non-payment of a collection?

If a client doesn't end up paying, you should set the the task to the `Unpaid` status. This will tell the program that you completed the work, but that it was never billed.

# How do I reconcile what I billed and what I got paid?

If a client only paid a fraction of an invoice, you have some options:

- Change the task's units: you can alter the collection, changing the units billed to `0`,
- Change your tax burdens: if your goal is more accurate tax reporting, you can add a deduction at for the difference between what you billed and what you were paid. For example, if you were owed were `$10,000` and only got paid `$6,000`, within your tax information for that year, a `$4,000` deduction effectively fixes your actual income since deductions come directly out of your billed amount for the year.

# What's the easiest way to import part data?

While there isn't a CSV importer at this time, you can create collections and bill out the exact amounts that you received against a single task (ie, you don't have to re-enter every task you've ever done). This gives you the benefit of accurate tax data.

See the "fixed-rate invoice" question for more details.

**Important**: Make sure you set the "completed on" date to the correct year the work was done on, otherwise it will be creditted to the current year.

# How do I setup a fixed-rate invoice?

- Create a new rate type with the exact amount of the invoice,
- Create a collection within the project you want to bill a fixed amount for,
- Create a single task with the new rate type,
- Set that task to any billable status

# What if I change my rates?

Always, always, always create a new rate type! Changing existing rates will result in old tax data being invalidated. "Delete" the old rate (it isn't actually deleted, just hidden since we need that for accurate calculations), and create a new rate with the same name + updated hourly.
