# ZermeloBladeGraphBlade
Blade based D3 Graph view for Zermelo reporting engine

How to get started using it
-------------------------

### Installation
1. On a working Laravel 5.5+ instance with [Zermelo](https://github.com/CareSet/Zermelo) installed, append the following
to the "repositories" section of your composer.json file so composer can find the package on Github:
```
    , 
    {
        "type": "git",
        "url": "https://github.com/CareSet/ZermeloBladeGraph.git"
    }
```        
2. Then run this command prompt at your project root, type (this requires installation of ssh keys to access private repo):
    `composer require careset/zermelobladegraph`
    
3. Then run:   
    `php artisan zermelo:install_zermelobladegraph`
This will create a zermelo directory in your resources directory containing blade view templates. 
This will also publish the configuration file to your app's config directory, move assets (js, css) to public/vendor. 
 
4. Make sure your database is configured in .env or your app's database.php config. 

### Running Example

There is a GraphTest report under the examples/report/ directory that you can use as a demonstration. 
You will need to import the data under examples/data/ into a database called graph_testdata in and give access to the reporting engine mysql user to that DB. 


### Basic functioning of the graph reporting system: 

First, in order to get a graph report, you must return one or several SQL statements that will respect that expected data structure for the Graph Reporting engine, like so: 
```
CREATE TABLE `graphdata_nodetypetests` (
  `source_id` varchar(50) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `source_size` int(11) NOT NULL DEFAULT 0,
  `source_type` varchar(255) NOT NULL,
  `source_group` varchar(255) NOT NULL,
  `source_longitude` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `source_latitude` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `source_img` varchar(255) DEFAULT NULL,
  `target_id` varchar(50) NOT NULL,
  `target_name` varchar(255) NOT NULL,
  `target_size` int(11) NOT NULL DEFAULT 0,
  `target_type` varchar(255) NOT NULL,
  `target_group` varchar(255) NOT NULL,
  `target_longitude` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `target_latitude` decimal(17,7) NOT NULL DEFAULT 0.0000000,
  `target_img` varchar(255) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 50,
  `link_type` varchar(255) NOT NULL,
  `query_num` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
```

With the following rules applied. 
Each node should be distinctive, by which I mean that the following rules should be followed: 

* each source_id and target_id is really an instance of a node_id. 
* in order to support many different type of ids, including uuids, the node_id field type is always a 50 character varchar.
* a given node_id can appear on either side of source_id and target_id
* whether a graph will be displayed as a "directed" graph or "undirected" is entirely dependant on the view. 
* each node_id should always have one and only one name, no matter when it is listed as a source or a target.
* each node_id should always have one and only one size, no matter where is appears. 
* each node_id should be in only one group, if this rule is ignored the last group will be used.   
* each node_id should always have the same lat/lon, which should cause error.
* each node_id should have one and only one type, which should cause error.

The Graph Reporting engine will expose the graph data in an "enriched D3 force-directed json", which should work just fine in [any standard D3 Force Directed example](https://bl.ocks.org/heybignick/3faf257bbbbc7743bb72310d03b86ee8)

This json format relies on the node position in a json array in order to draw nodes and connections, and while the node_id is transmitted in the json
The actually edges array in the json is based on the position of the nodes in the json['nodes'] array. 

### The power of multiple SQL queries
While it is likely possible to make one massive SQL query that creates the entire "graph table", the power of the reporting engine is that you can simply add additional edges to the displayed
graph by adding new nodes and edges using a new SQL command. 

This way, you can focus on one type of node to node connection, model it in SQL, and then see the resulting graph in a force-directed and browseable visualization. 
Using this as a tool, you can perform sophisticated graph analytics, but power it underneath with SQL. 

This is alot of power, and it is easy to shoot yourself in the foot. One must be careful to ensure that the underlying SQL really is capturing all of the node relationships that are see on the graph 
anaylysis interface. This feature helps to handle the tablular-graph analysis impedence mis-match.. but it does not eliminate it and it when it is not handled correctly. 
It can create a data view that looks right, but is not actually reflective of the full data structure. We would love to have help and contributions to make this less of a 
[Leaky Abstraction](https://en.wikipedia.org/wiki/Leaky_abstraction) but while the abstraction is leaky, it is also very powerful and in many cases eliminates the 
need for graph database for "small network" graph analysis. 




### To access your web routes (default):

Displays d3 graph view
``` [base_url]/ZermeloGraph/[ReportClassName]```

Example Report d3 graph view
``` [base_url]/ZermeloGraph/GraphTest```

### NOTES
This package automatically requires the Zermelo package as a dependency. You will nedd to follow the zermelo
installation instructions on how to subclass ZermeloReport and configure your app to use zermelo.
