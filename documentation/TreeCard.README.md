# ZermeloBladeTreeCard
Blade based bootstrap 4.3+ "tree card"   based view for Zermelo reporting engine.
Basically this view allows for a table to take this specific format:

| root | root_url | branch | branch_url | leaf | leaf_url
-------|----------|--------|------------|------|----------
Apps   |          | Music  |            | Shazam | https://www.shazam.com  
Apps   |          | Music  |            | Amazon Music | https://music.amazon.com  
Apps   |          | Music  |            | Spotify | https://www.spotify.com  
Apps   |          | Music  |            | Napster | https://www.napster.com  
Apps   |          | Video  |            | Netflix | https://netflix.com  
Apps   |          | Video  |            | Youtube | https://youtube.com  
Web   |          | Search  |            | Yahoo | https://www.yahoo.com  
Web   |          | Search  |            | Dogpile | https://www.dogpile.com  
Web   |          | Search  |            | Google | https://www.google.com  
Web   |          | Social  |            | Twitter | https://www.napster.com  
Web   |          | Social  |            | Reddit | https://reddit.com  
 
And turn into this: (screenshot soon)
 
How to get started using it
-------------------------

### Installation

This package requires the Zermelo package. You will nedd to follow the [zermelo installation instructions](https://github.com/CareSet/Zermelo) 
on how to configure your app to use zermelo.

After that is complete, use composer to install, and artisan to configure the Tree Card Report

```
composer require careset/zermelobladetreecard
```

Then use artisan to configure it

```
php artisan zermelo:install_zermelobladetreecard
```

This may ask you to confirm the replacement of some assets... you can safely choose to replace or not replace, it makes little difference. 



### How the cards work
The cards are based on the [bootstrap card system](https://getbootstrap.com/docs/4.1/getting-started/introduction/) implemented using blade templates..



### Run an example
To test if you have installed correctly, see if you have Zermelo Card urls in your route using artisan..
```
./artisan route:list | grep ZermeloTreeCard
```

You should see 3 different routes in the result... if you see nothing.. something has gone wrong...

Then copy over the example data and reports to your installation
```
mysqladmin -u YOURDBUSER -p create zermelo_tree_cards
mysql -u YOURDBUSER -p zermelo_tree_cards < vendor/careset/zermelobladetreecard/examples/data/zermelo_tree_cards.sql
cp vendor/careset/zermelobladetreecard/examples/reports/TreeCardTest.php app/Reports/
```

That will create the zermelo_tree_cards test database, populate it with example data... 
And copy the Card test report to your installation...

Remember that you will need to ensure that your laravel DB user has SELECT access to the zermelo_cards database..

Then point your browser to

https://example.com/ZermeloTreeCard/TreeCardTest/

and you should see the content


