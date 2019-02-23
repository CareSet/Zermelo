# Sprint End Tests

This document details the basic instructions determining if a new branch of Zermelo is ready for merging back into the
master branch. 

## Install Tests
* Checkout a fresh copy of the latest long-term support version of Laravel, Laravel 5.5, as per [instructions](https://laravel.com/docs/5.5/installation)
* Using only the steps outlined in the Readme. Install the Zermelo reporting engine. 
* Using only the steps outlined in the Readme. Configure a new example report. 

## Use Tests
* For every "stable" view type
  * verify that the view actually works in a web-browser and gives the right results.
  * (Soon) copy the test reports into the reports directory, verify that all tests reports work
  
* Make sure CSV download works with Cache on and off
  
In the event that the ReadMe and the results do not line up, either change the ReadMe to reflect how things "now work" or
fix the code to work the way the ReadMe says. 


