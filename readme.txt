=== NS Google Sheets Connector ===
Contributors: neversettle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RM625PKSQGCCY&rm=2
Tags: google, sheets, google sheets, connector, integration, cf7, contact form 7, data, db, form, form data
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a painless way to integrate and automatically send WordPress data to Google Sheets.  

== Description ==

This plugin currently supports connecting the Contact Form 7 plugin to Google Sheets, but let us know what other kinds of data you'd like this to capture! 

= How to Use this Plugin =

(Take a look at the screenshot to see how it all ties together and what settings need to go where!)

1. Install the Contact Form 7 (CF7) plugin if you haven't already. 
1. Create the CF7 Form that you want to use to capture data. You will need the ID and field names from the form.
1. Log in to your Google Account and create a new Sheet and give it a simple name. 
1. Rename Sheet 1 (tab 1 of the spreadhseet) to something simple that makes sense (maybe the name of your form).
1. Add a column name in Row 1 for "date" and each form field you will have in your form (default CF7 form field names are "your-name", "your-email", "your-subject", "your-message"). 
1. Install this plugin.
1. Edit the plugin settings and copy / paste the name of your spreadsheet and worksheet into the settings under Settings > NS Sheets.
1. Add the ID of the form you want to use in the settings.
1. Save your settings.
1. Test your form submit and verify that the data shows up in your Google Sheet.
1. Have a beer and celebrate! 

= Important Notes = 

* You must pay very careful attention to your naming. This plugin will have unpredictable results if names and spellings do not match between your Google Sheets and plugin / form settings.
* Feedback is really important to us. Let us know if there are other creative ways you want to use this.  

Enjoy!

== Installation ==

1. Log in to your WordPress site as an administrator
2. Use the built-in Plugins tools to install from the repository or unzip and Upload the plugin directory to /wp-content/plugins/ 
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The current output of the plugin teamplate can be seen by going to Settings > NS Plugin Template

== Screenshots ==

1. Plugin Settings and How to Configure your Google Spreadsheet and your Contact Form 

== Frequently Asked Questions ==

= Is this plugin template supported? =
We'll try to answer any questions that come up in the support forum here on WP.org, but can't promise support. 

== Changelog ==

= 1.0.0 =
* First public release

== Upgrade Notice ==

