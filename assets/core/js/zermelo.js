/*
 This is the core zermelo JS library for communication with server
 It handles:
    * Sockets/Data View Options
    * Search Filter Options
    * Download URI generation
    * Executing the download request to the server
 */
var Zermelo = function( reportURI, downlaodURI, options ) {

    this.downloadURI = downlaodURI;
    this.reportURI = reportURI;
    this.downloadFileType = 'excel';

    /**
     *  These are optional parameters passed in at instantiation
     *
     *  We list them out here with defaults so you know what's available
     */
    this.options = options || {
        'token': null,
        'passthrough_params': {}
    };

    /**
     *  These are the events that can be "subscribed" to
     * @type {[string]}
     */
    this.events = {
        'download.done': []
    };

    this.sockets = [];

    this.searchFilters = [];

    this.URLParameters = [];

    var that = this;

    this.on = function(event, callback) {

        if ( !event in that.events ) {
            throw "ERROR: "+event+" is not a valid event";
        }

        that.events[event].push(callback);

        // return our object so we can chain events
        return that;
    };

    this.do = function(event) {
        var list = that.events[event];
        if ( !list || !list[0] ) {
            return;
        }

        var args = list.slice.call(arguments, 1);
        list.slice().map(function(i) {
           i.apply(that, args);
        });
    };

    this.pushSocket = function(socketId, wrenchId) {
        that.sockets.push({
            wrenchId: wrenchId,
            socketId: socketId
        });
    };

    this.setSockets = function(newSockets) {
        that.sockets = newSockets;
    }

    /**
     *  Get all search filters, including column and global search filters
     *  Search filters are stored in key/value pairs like this:
     *  [
     *      { search_column_1: value },
     *      { search_column_2: value }
     *  ]
     *
     *  This format is a legacy API format, and could be changed in the future
     *  to be more developer-friendly by using a format similar to sockets.
     *
     * @returns {Array}
     */
    this.getSearchFilters = function() {
        return that.searchFilters;
    }

    this.clearSearchFilters = function() {
        return that.searchFilters = [];
    }

    this.pushColumnSearchFilter = function(columnKey,value) {
        var pair = {};
        pair[ columnKey ] = value;
        that.searchFilters.push(pair);
    }

    this.pushGlobalSearchFilter = function(value) {
        if ( value != "" ) {
            that.searchFilters.push({
                "_": value
            });
        }
    }

    /**
     * Get an array of parameters from the Address bar
     *
     * @returns {URLSearchParams}
     */
    this.getUrlSearchParams = function() {
        let urlParams = new URLSearchParams(window.location.search);
        let entries = urlParams.entries();
        that.URLParameters = [];
        for ( var keyval of entries ) {
            let pair = {};
            pair[keyval[0]] = keyval[1];
            that.URLParameters.push(pair);
        }
        return that.URLParameters;
    };

    /**
     * Set the download file type to excel or csv
     * @param download_file_type
     *
     */
    this.setDownloadFileType = function(download_file_type) {
        if (download_file_type == 'excel' || download_file_type == 'csv') {
            this.downloadFileType = download_file_type
        }
    }

    /**
     * This will return a URL that contains both address-bar GET parameters
     * and AJAX filters that have been applied via datatables
     */
    this.getDownloadURI = function() {

        // Build an object for the API call
        let api_data = {
           // "_token" : that.options._token, // CSRF token
            "filter": that.searchFilters, // Search filters
            "sockets": that.sockets, // Pass sockets for "Data Options"
            "download_file_type": that.downloadFileType
        };

        // Get an associative array of parameters from the address bar
        // We need to convert our array of key/value pairs into an associative array
        let passthrough_params = {};
        $.each( that.getUrlSearchParams(), function (key, option) {
            for (var i in option) {
                passthrough_params[i] = option[i];
            }
        });


        // Merge the URI Parameters with the ajax parameters (datatable search filters)
        let merged_parameters = $.extend({}, passthrough_params, api_data);

        // Get the complete download URI by using param() to build
        // query string from merged params and appending it to the report's download URI
        let downloadUri = window.location.protocol + "//" + window.location.host +
            that.downloadURI + "?" + decodeURIComponent( $.param( merged_parameters ) );

        return downloadUri;
    }

    /**
     *
     * @param downloadURI
     * @param params
     *
     * Perform a download to the Zermelo API using the current report
     * including parameters and filters
     */
    this.serverDownloadRequest = function() {

        // Get the FULL download URI, including parameters
        let downloadURI = that.getDownloadURI();

        // Open the download in a new 'tab'
        window.open(downloadURI, '_blank');

        // Functions available to this method
        return {
            // Pass your callback to done for callback to be called when the download is done
            done: function( callback ) {
                that.on('download.done', callback);

                // Tell our client that the download is done in case they want to do something
                that.do('download.done');
            }
        }
    };

    /**
     * Export public methods
     */
    return {
        on: this.on,
        serverDownloadRequest: this.serverDownloadRequest,
        pushSocket: this.pushSocket,
        setSockets: this.setSockets,
        setDownloadFileType: function(download_file_type) {
            return that.setDownloadFileType(download_file_type)
        },
        getDownloadURI: this.getDownloadURI,
        pushGlobalSearchFilter: this.pushGlobalSearchFilter,
        pushColumnSearchFilter: this.pushColumnSearchFilter,
        getSearchFilters: this.getSearchFilters,
        clearSearchFilters: this.clearSearchFilters,
        getUrlSearchParams: this.getUrlSearchParams,
        getAllApiParams : function () {
            var api_params = $.extend({}, this.getPassthroughParams(), {'token':that.options.token});
            return api_params;
        },
        getPassthroughParams: function () {
            return that.options.passthrough_params ? that.options.passthrough_params : {};
        },
        getPassthroughParam: function(key) {
            let params = this.getPassthroughParams();
            if (key in params) {
                return params[key];
            } else {
                console.log("Error: There is no key `"+key+"` in the parameter array.")
            }
        }
    }
};

