<div style="float:right; width:200px; background-color: #fff; border: 1px solid #e5e5e5;padding:24px; margin-left: 24px; margin-bottom: 24px;">

- [Overview](#overview)
- [Generating a Report](#generating-a-report)
- [Creating Custom Reports](#creating-custom-reports)
  - [Templates](#templates)
  - [Example Use Cases](#example-use-cases)
    - [Project hand off checklist](#project-hand-off-checklist)
    - [Project remaining backlog list](#project-remaining-backlog-list)
</div>

# Overview

A report is a filtered list of tasks that can be generated, printed, and sent to your client.

You can choose to add 

# Generating a Report

You can generate a report by going to any project and clicking on `Generate Report`.

# Creating Custom Reports

Reports a module-based system, allowing you to create as many report templates as you'd like.

## Templates

Create a PHP template file and save it into the `templates/report` directory. Ideally, use `snake_case.php` to name your file.

You can use the default template as the basis of your custom templates.

Please see the "[Objects](objects.md)" article for information on what variables are available on report templates.

## Example Use Cases

### Project hand off checklist

- Using the "Default Template":
  - Create a story type called "Hand Off"
  - Generate a report with all stories considered hand off items following transfer of the project back to the client.
     
### Project remaining backlog list

- Using the "Default Template":
  - Generate a report with all "Open"
  - Optionally limit which story types will be included
  - You can now send your client the remaining backlog items!