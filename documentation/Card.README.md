# ZermeloBladeCard
Blade based bootstrap 4.1+ card based view for Zermelo reporting engine

How to get started using it
-------------------------

### Installation

This package requires the Zermelo package. You will nedd to follow the [zermelo installation instructions](https://github.com/CareSet/Zermelo)
on how to configure your app to use zermelo.

After that is complete, use composer to install, and artisan to configure the Card Report

```
composer require careset/zermelobladecard
```

Then use artisan to configure it

```
php artisan install:zermelobladecard
```

This may ask you to confirm the replacement of some assets... you can safely choose to replace or not replace, it makes little difference.



### How the cards work
The cards are based on the [bootstrap card system](https://getbootstrap.com/docs/4.1/getting-started/introduction/) implemented using blade templates..

There are several functions that appear in a card report that do not appear in other report types, specifically:

* The is_fluid() function determines if a fluid layout will be used
* the cardWidth() function sets the default width of every card (which you can control with data by including a card_width in the report. Both of these will support valudes that belong in a "style" parameter of the card div. Like style='width: 200px' or style='width: 80%'. In this case, you would return just '200px' or '80%' in either the cardWidth function or in the card_width data field below.

### Run an example
To test if you have installed correctly, see if you have Zermelo Card urls in your route using artisan..
```
./artisan route:list | grep ZermeloCard
```

You should see 3 different routes in the result... if you see nothing.. something has gone wrong...

Then copy over the example data and reports to your installation
```
mysqladmin -u YOURDBUSER -p create zermelo_cards
mysql -u YOURDBUSER -p zermelo_cards < vendor/careset/zermelobladecard/examples/data/zermelo_cards.sql
cp vendor/careset/zermelobladecard/examples/reports/CardTest.php app/Reports/
```

That will create the zermelo_cards test database, populate it with example data...
And copy the Card test report to your installation...

Remember that you will need to ensure that your laravel DB user has SELECT access to the zermelo_cards database..

Then point your browser to

https://example.com/ZermeloCard/CardTest/

and you should see the content

### The Card Syntax

```
	//Card Layout Columns
        //this is based on the cards element from bootstrap https://getbootstrap.com/docs/4.3/components/card/ in the standard view
        //card_header is text at the top card.
        //card_title will be the title of the card, inside the card content
        //card_text is beneath the title in the card content
        //card_img_top is the image url for an image placed at the top of the card
        //card_img_bottom is the image url for the bottom
        //card_img_top_alttext sets the alttext of the image at the top
        //card_img_bottom_alttext sets the alttext of the image at the bottom
        //card_footer is the text inside the footer of the card
	//card_body is any card-body content that will come after card-title, card-text
	//card_body_class is any additional css classes to control how the card-body div will act (the card-body div contains the card_title, card_text, and then anything you put into card_body
	//card_width the width of this card. If set will override the value from the cardWidth() function which can be used to set a default for the report... 

	//Card Block Layout Columns
	//card_layout_block_id this is any identifier you would like to use to label a sequential group of cards... by changing this for a sequential group 
	//	The cards will move to a new row (if they have not already) and they will switch between black text/white background to 
	//	white text on a grey background.. this is basically the same as the 'zebra' effect that is often used on. If this is not set, the cards 
	//	simply all use the black text/white background for all the cards...
	//card_layout_block_label this will be used as the heading before a new grouping of cards, if it is set.
	//card_layout_block_url if you have a block label, you can turn it into a link by setting this field..

        $sql = "
SELECT
        id AS card_header,
        title AS card_title,
        text AS card_text,
        image_url AS card_img_top,
        alt_text As card_img_top_altext,
        id AS card_footer
FROM zermelo_cards.cards
";


```

<img src='/docs/Zermelo_card_layout_visual.2.png' width=400>






