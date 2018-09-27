# Trust Indicators Plugin

## Trust Indicators Plugin Description

The Trust Indicators plugin allows you to store and display additional information (trust indicators) to convey the trustworthiness of your organization, authors, and articles.

This plugin was funded by and built in collaboration with the [Trust Project](https://thetrustproject.org/). For a history of changes to this plugin, see [CHANGELOG.md](./CHANGELOG.md).

### Table of Contents
  * [Article/Post Indicators](#article-indicators)
  * [Best Practices Policies](#best-practices)
  * [Author Indicators](#author-indicators)
  * [Shortcodes](#shortcodes)
  * [Installation](#installation)

<a id="article-indicators"></a>
## Article/Post Indicators
_When creating a new post or editing an existing post, you will find the following fields available to customize at the bottom of the edit screen, below the main body of content._

![Post Indicator Settings](https://github.com/INN/trust-project-partner/blob/master/assets/trust-postindicators-backend.png)

- Corrections
  - Enter corrections to be displayed along with the post. 
  - This will show exactly as it is formatted in the edit box (list, paragraphs, etc.). Use the WYSIWYG to format.
- Sourcing and Methodology Statement
  - Information for the reader about how the idea for the story was developed. The suggested length for this field is 1200 characters, including spaces.
- Citations and References
  - Enter one URL per line. 
  - Provide URLs to internal or external documents, related stories, and sources gathered by the newsroom.
- Dateline (location)
- Type of Work
  - The plugin prepopulates a number of “Type of Work” taxonomies, which you can find under Posts > Type of Work and in the “Type of Work” metabox on any post-edit screen.

"Type of Work" in the post-edit screen:

![Type of work in post-edit screen](https://github.com/INN/trust-project-partner/blob/master/assets/trust-typeofwork-postedit.png)

"Type of Work" categories populated in the settings:

![Type of work categories](https://github.com/INN/trust-project-partner/blob/master/assets/trust-project-typesofwork.png)

### What this looks like to readers:
![Indicators](https://github.com/INN/trust-project-partner/blob/master/assets/trust-behindthestory.png)

![Behind the story](https://github.com/INN/trust-project-partner/blob/master/assets/trust-behindthestory-2.png)

<a id="best-practices"></a>
## Best Practices Policies
For more information on why Best Practices Policies are recommended and guidelines on how to create your own, see the following:

[The Trust Indicators Protocol](https://www.scu.edu/ethics/focus-areas/journalism-ethics/programs/the-trust-project/collaborator-materials/)

[Best Practices Guidelines](https://docs.google.com/document/d/1jdt4V92XtveciID3TBl79aiwQcYs5uGSDVdN72PGcpw/edit)

_To access the settings for the Best Practices Policies, go to WP Dashboard > Settings > Trust Indicators._

![Trust Indicator Settings](https://github.com/INN/trust-project-partner/blob/master/assets/trust-settings.png)

### Sitewide Policies
- Policies on single page? (checkbox) 
  - Are your policies currently listed on a single page rather than separate pages on your website? If so, check this box. 
  - If your policies are listed on a single page, you will need to provide jump links (anchor tags) which link to the section of the page that contains the specific policy. Those links are filled out in the fields below:
- Ethics Policy (link)
- Diversity Statement (link)
- Diversity Staffing Report (link)
- Corrections Policy (link)
- Ownership Structure, Funding (link)
- Founding Date (date)
- Masthead (link)
- Mission Statement with Coverage Priorities (link)
- Fact-checking Standards (link)
- Unnamed Sources Policy (link)
- Publishing Principles (link)
- Actionable Feedback
- Bylines: This site uses bylines (checkbox)
  - If your site is currently using bylines, then check the box above.
  - If not, you will need to create and share a policy for why you do not use bylines.
- Newsroom Contact Info

### What this looks like to readers:
Editorial standards link in sidebar:

![Editorial Standards](https://github.com/INN/trust-project-partner/blob/master/assets/trust-editorialstandards.png)

Lightbox with policies:

![Policies](https://github.com/INN/trust-project-partner/blob/master/assets/trust-editorialstandards-popup.png)

<a id="author-indicators"></a>
## Author Indicators
_There are specific trust indicator settings available to authors. They can be found in each user’s profile. To access user profile/author settings, go to: WP Dashboard > Users > Edit User > Trust Indicators._

![Author Indicator Settings](https://github.com/INN/trust-project-partner/blob/master/assets/trust-authorindicators-backend.png)

- Location
- Languages Spoken
  - Enter a comma-separated list of languages you are fluent in.
- Areas of Expertise
  - Enter a list of topics, demographics, and geographic regions where you are considered a subject matter expert. One entry per line.
- Location Expertise
  - Enter one geographic location per line in this format: City, State/Province, Country. (For example: Chicago, Illinois, USA)
- Official Title
  - (Affiliation with Publisher)
- Phone Number
  - Public-facing and must include an international country code prefix.
- Email Address
  - (Public-facing)
- Twitter Profile
- Linkedin Profile

### What this looks like to readers:
![Author Indicators Front-end](https://github.com/INN/trust-project-partner/blob/master/assets/trust-abouttheauthor.png)

<a id="shortcodes"></a>
## Shortcodes
The plugin ships with a [trust-indicators] shortcode that can be used to display any information the plugin is storing within a post. For more information on how to use these shortcodes, go [here](https://github.com/INN/trust-indicators/wiki/Shortcodes).

Example of shortcode:

![Example of shortcode](https://github.com/INN/trust-project-partner/blob/master/assets/trust-project-shortcode.png)

<a id="installation"></a>
## Installation

The following installation instructions apply to WordPress sites in general. Consult your hosting provider if they have specific requirements for approval of plugins.

1. Download the source code of [the latest release](https://github.com/INN/trust-indicators/releases) and unzip it.
2. Upload the entire `trust-indicators` folder and its contents to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

If you experience problems with the plugin, please email trustwp@inn.org.
