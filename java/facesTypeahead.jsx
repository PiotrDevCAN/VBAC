//ciolab.ibm.com/misc/typeahead/builds/facestypeahead-0.4.4.js
if(window.dojo){
dojo.provide("dojo.NodeList-data");
(function(d){
	
/*=====
	dojo.NodeList.prototype.data = function(key, value){
		// summary: stash or get some arbitrary data on/from these nodes. 
		//
		// description:
		//		Stash or get some arbirtrary data on/from these nodes. This private _data function is
		//		exposed publicly on `dojo.NodeList`, eg: as the result of a `dojo.query` call.
		//		DIFFERS from jQuery.data in that when used as a getter, the entire list is ALWAYS
		//		returned. EVEN WHEN THE LIST IS length == 1.
		//
		//		A single-node version of this function is provided as `dojo._nodeData`, which follows
		//		the same signature, though expects a String ID or DomNode reference in the first 
		//		position, before key/value arguments.
		//
		// node: String|DomNode 
		//		The node to associate data with
		//
		// key: Object?|String?
		//		If an object, act as a setter and iterate over said object setting data items as defined.
		//		If a string, and `value` present, set the data for defined `key` to `value`
		//		If a string, and `value` absent, act as a getter, returning the data associated with said `key`
		//
		// value: Anything?
		//		The value to set for said `key`, provided `key` is a string (and not an object)
		//
		// example:
		//		Set a key `bar` to some data, then retrieve it.
		//	|	dojo.query(".foo").data("bar", "touched");
		//	|	var touched = dojo.query(".foo").data("bar");
		//	|	if(touched[0] == "touched"){ alert('win'); }
		//
		// example:
		//		Get all the data items for a given node. 
		//	|	var list = dojo.query(".foo").data();
		//	|	var first = list[0];
		//
		// example:
		//		Set the data to a complex hash. Overwrites existing keys with new value
		//	|	dojo.query(".foo").data({ bar:"baz", foo:"bar" });
		//		Then get some random key:
		//	|	dojo.query(".foo").data("foo"); // returns [`bar`]
		//
		//	returns: Object|Anything|Nothing
		//		When used as a setter via `dojo.NodeList`, a NodeList instance is returned 
		//		for further chaning. When used as a getter via `dojo.NodeList` an ARRAY
		//		of items is returned. The items in the array correspond to the elements
		//		in the original list. This is true even when the list length is 1, eg:
		//		when looking up a node by ID (#foo)
	};
	
	dojo.NodeList.prototype.removeData = function(key){
		// summary: Remove the data associated with these nodes.
		// key: String?
		//		If ommitted, clean all data for this node.
		//		If passed, remove the data item found at `key`
	};
	
	dojo._nodeDataCache = {
		// summary: An alias to the private dataCache for NodeList-data. NEVER USE THIS!
		//		This private is only exposed for the benefit of unit testing, and is 
		//		removed during the build process.
	};
	
=====*/

	var dataCache = {}, x = 0, dataattr = "data-dojo-dataid", nl = d.NodeList,
		dopid = function(node){
			// summary: Return a uniqueish ID for the passed node reference
			var pid = d.attr(node, dataattr);
			if(!pid){
				pid = "pid" + (x++);
				d.attr(node, dataattr, pid);
			}
			return pid;
		}
	;
	
	//>>excludeStart("debugging", true);
	// exposed for unit tests:
	d._nodeDataCache = dataCache;
	//>>excludeEnd("debugging");
	
	var dodata = d._nodeData = function(node, key, value){

		var pid = dopid(node), r;
		if(!dataCache[pid]){ dataCache[pid] = {}; }
		
		// API discrepency: calling with only a node returns the whole object. $.data throws
		if(arguments.length == 1){ r = dataCache[pid]; }
		if(typeof key == "string"){
			// either getter or setter, based on `value` presence
			if(arguments.length > 2){
				dataCache[pid][key] = value;
			}else{
				r = dataCache[pid][key];
			}
		}else{
			// must be a setter, mix `value` into data hash
			// API discrepency: using object as setter works here
			r = d._mixin(dataCache[pid], key); 
		}
		
		return r; // Object|Anything|Nothing
	};
	
	var removeData = d._removeNodeData = function(node, key){
		// summary: Remove some data from this node
		// node: String|DomNode
		//		The node reference to remove data from
		// key: String?
		//		If omitted, remove all data in this dataset.
		//		If passed, remove only the passed `key` in the associated dataset
		var pid = dopid(node);
		if(dataCache[pid]){
			if(key){ 
				delete dataCache[pid][key];
			}else{ 
				delete dataCache[pid];
			}
		}
	};
	
	d._gcNodeData = function(){
		// summary: super expensive: GC all data in the data for nodes that no longer exist in the dom. 
		// description:
		//		super expensive: GC all data in the data for nodes that no longer exist in the dom. 
		//		MUCH safer to do this yourself, manually, on a per-node basis (via `NodeList.removeData()`)
		//		provided as a stop-gap for exceptionally large/complex applications with constantly changing
		//		content regions (eg: a dijit.layout.ContentPane with replacing data)
		//		There is NO automatic GC going on. If you dojo.destroy() a node, you should _removeNodeData 
		//		prior to destruction.
		var livePids = dojo.query("[" + dataattr + "]").map(dopid);
		for(var i in dataCache){
			if(dojo.indexOf(livePids, i) < 0){ delete dataCache[i]; }
		}
	};

	// make nodeData and removeNodeData public on dojo.NodeList:
	d.extend(nl, {
		data: nl._adaptWithCondition(dodata, function(a){
			return a.length === 0 || a.length == 1 && (typeof a[0] == "string");
		}),
		removeData: nl._adaptAsForEach(removeData)
	});

// TODO: this is the basic implemetation of adaptWithCondtionAndWhenMappedConsiderLength, for lack of a better API name
// it conflicts with the the `dojo.NodeList` way: always always return an arrayLike thinger. Consider for 2.0:
//
//	nl.prototype.data = function(key, value){
//		var a = arguments, r;
//		if(a.length === 0 || a.length == 1 && (typeof a[0] == "string")){
//			r = this.map(function(node){
//				return d._data(node, key);
//			});
//			if(r.length == 1){ r = r[0]; } // the offending line, and the diff on adaptWithCondition
//		}else{
//			r = this.forEach(function(node){
//				d._data(node, key, value);
//			});
//		}
//		return r; // dojo.NodeList|Array|SingleItem
//	};
	
})(dojo);}/**
 * QueryHelper.js
 * @author Tim Finley
 * @author Ryan Silva
 * QueryHelper is an abstraction of the query functionality in JQuery and Dojo.
 * The CIO Lab prefers to use JQuery, but for other reasons Dojo needs to be
 * used.  Instead of relying on one or the other, we abstract out common 
 * functionality that we'll need in our portable plugins like Typeahead.  That
 * way Typeahead (or any other features) can work with either Dojo or JQuery.
 */
(function() {

// Helpers to abstract out dojo and jquery

var h = window.QueryHelper = {};

if (window.jQuery) {
    h.queryAll = $;
    
    h.query = function(element, selector) {
        return element.find(selector);
    };
    
    h.extendObject = $.extend;
    h.trimString = $.trim;
    
    h.getValue = function(element) {
        return element.val();
    };
    
    h.setValue = function(element, str) {
        return element.val(str);
    };
    
    h.getAttr = function(element, name) {
        return element.attr(name);
    };
    
    h.setAttr = function(element, name, value) {
        element.attr(name, value);
    };
    
    h.fetchJSON = function(options) {
        options.dataType = 'json';
        options.url += '?callback=?';
        return $.ajax(options);
    };
    
    h.getData = function(element, key) {
        return element.data(key);
    };
    
    h.setData = function(element, key, value) {
        return element.data(key, value);
    };
    
    h.multiBind = function(element, events, func) {
        return element.bind(events, func);
    };
    
    h.isVisible = function(element) {
        return element.is(':visible');
    };
    
    h.isHidden = function(element) {
        return element.is(':hidden');
    };
    
    h.changeCSS = function(element, obj) {
        return element.css(obj);
    };
    
    h.show = function(element) {
        element.show();
    };
    
    h.hide = function(element) {
        element.hide();
    };
    
    h.setText = function(element, str) {
        element.text(str);
    };
    
    h.setHTML = function(element, str) {
        element.html(str);
    };
    
    h.hasAncestor = function(rootElement, elementToLookFor) {
        return rootElement.parents().filter(elementToLookFor).length > 0;
    };
    
    h.onDocumentReady = $;

    h.map = $.map;
    
    h.offset = function(element) {
        return element.offset();
    };
    
    h.hasClass = function(element, clz) {
        return element.hasClass(clz);
    };
    
    h.trigger = function(eventStr, element) {
        return element.trigger(eventStr);
    };
}
else if (window.dojo) {
    dojo.require("dojo.io.script");
    dojo.require('dojo.NodeList-data');
    dojo.require('dojo.NodeList-manipulate');
    dojo.require('dojo.NodeList-traverse');
    
    h.queryAll = dojo.query;
    
    h.query = function(element, selector) {
        return element.query(selector);
    };
    
    h.extendObject = dojo.mixin;
    h.trimString = dojo.trim;
    
    h.getValue = function(element) {
        return element.val();
    };
    
    h.setValue = function(element, str) {
        return element.val(str);
    };    
    
    h.getAttr = function(element, name) {
        return dojo.attr(element[0], name);
    };
    
    h.setAttr = function(element, name, value) {
        dojo.attr(element[0], name, value);
    };
    
    h.fetchJSON = function(options) {
        // dojo already supports timeout and error
        options.url = options.url + '?' + dojo.objectToQuery(options.data);
        delete options.data;
        
        options.callbackParamName = 'callback';
        // options.handleAs = 'json';
        
        var success = options.success;
        delete options.success;
        options.load = function(json, data) {
            return success(json, undefined, data.xhr);
        };
        
        // options.headers = {
        //     'X-Requested-With': ''
        // };
        // 
        // var deferred = dojo.xhrGet(options);
        // 
        // return deferred.ioArgs.xhr;
        dojo.io.script.get(options);
    };

    // Uses the dojo.NodeList-data module
    h.getData = function(element, key) {
        return element.data(key)[0];
    };
    
    h.setData = function(element, key, value) {
        return element.data(key, value);
    };
    
    h.multiBind = function(element, events, func) {
        var eventArray = events.split(' ');
        
        for (var i = 0, l = eventArray.length; i < l; i++) {
            dojo.connect(element[0], 'on' + eventArray[i], func);
        }
    };
    
    // Stolen from https://gist.github.com/137880/af888c9295aedcb8bb66c39983e7c75e7555c729
    function isHiddenHelper(domElement) {
        var w = domElement.offsetWidth, h = domElement.offsetHeight,
        force = (domElement.tagName === 'TR');
        return (w===0 && h===0 && !force) ? true : (w!==0 && h!==0 && !force) ? false : domElement.getStyle('display') === 'none';
    };
    
    h.isVisible = function(element) {
        return !isHiddenHelper(element[0]);
    };
    
    h.isHidden = function(element) {
        return isHiddenHelper(element[0]);
    };
    
    h.changeCSS = function(element, obj) {
        //dojo.style(element[0], obj);
        return element.style(obj);     //Not sure why Tim used the above notation instead.
    };
    
    // Totally ghetto show and hide funcs ripped and hacked from jquery (also use the dojo.NodeList-data module)
    
    var elemdisplay = {};
    
    function defaultDisplay( nodeName ) {
        if ( !elemdisplay[ nodeName ] ) {
            var elem = document.createElement(nodeName),
                body = dojo.query('body');
                
            body.append(elem);
            
            var display = dojo.style(elem, "display");

            body[0].removeChild(elem);

            if ( display === "none" || display === "" ) {
                display = "block";
            }

            elemdisplay[ nodeName ] = display;
        }

        return elemdisplay[ nodeName ];
    }
    
    h.show = function(element) {
        // element.style({ display: '' });
        
        for ( var i = 0, j = element.length; i < j; i++ ) {
            elem = element[i];
            elemInNodeList = dojo.query(elem);
            display = elem.style.display;

            // Reset the inline display of this element to learn if it is
            // being hidden by cascaded rules or not
            if ( !elemInNodeList.data("olddisplay")[0] && display === "none" ) {
                display = elem.style.display = "";
            }

            // Set elements which have been overridden with display: none
            // in a stylesheet to whatever the default browser style is
            // for such an element
            if ( display === "" && dojo.style( elem, "display" ) === "none" ) {
                elemInNodeList.data("olddisplay", defaultDisplay(elem.nodeName));
            }
        }

        // Set the display of most of the elements in a second loop
        // to avoid the constant reflow
        for ( i = 0; i < j; i++ ) {
            elem = element[i];
            display = elem.style.display;

            if ( display === "" || display === "none" ) {
                elem.style.display = elemInNodeList.data("olddisplay")[0] || "";
            }
        }
    };
    
    h.hide = function(elements) {
        // elements.style({ display: 'none' });
        
        for ( var i = 0, j = elements.length; i < j; i++ ) {
            var display = dojo.style( elements[i], "display" ),
                elemInNodeList = dojo.query(elements[i]);

            if ( display !== "none" ) {
                elemInNodeList.data("olddisplay", display );
            }
        }

        // Set the display of the elements in a second loop
        // to avoid the constant reflow
        for ( i = 0; i < j; i++ ) {
            elements[i].style.display = "none";
        }
    };
    
    h.setText = function(element, str) {
        element.text(str);
    };    
    
    h.setHTML = function(element, str) {
        element.html(str);
    };
        
    h.hasAncestor = function(rootElement, ancestorToLookFor) {
        return dojo.isDescendant(rootElement[0], ancestorToLookFor[0]);
    };

    h.onDocumentReady = function(func) {
        dojo.addOnLoad(func);
    };

    h.map = function(arr, func) {
        var results = [],
            mapped = dojo.map(arr, func);
        
        // dojo map doesn't remove undefined elements
        for (var i = 0, l = mapped.length; i < l; i++) {
            if (mapped[i] !== undefined)
                results.push(mapped[i]);
        }
        
        return results;
    };    
    
    h.offset = function(element) {
        var position;
        //Dojo 1.4 and higher
        if ( dojo.position ) {
            //parameters: element, includeScroll
            position = dojo.position(element[0], true);
        }
        //ODW is on Dojo 1.3, which has this function instead...
        else {
            position = dojo.coords(element[0],  true);
        }
        
        return {
            top: position.y,
            left: position.x
        };
    };
    
    h.hasClass = function(element, clz) {
        return element.length > 0 && dojo.hasClass(element[0], clz);
    };
    
    h.trigger = function(eventStr, element) {
    	for(var i = 0; i < element.length; i++){
			if ("fireEvent" in element[i]){
				element[i].fireEvent("on" + eventStr);
			} else {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent(eventStr, false, true);
				element[i].dispatchEvent(evt);
			}
		}
    };
}
})();

/**
 * The "bind()" function extension from Prototype.js, extracted for general use
 *
 * @author Richard Harrison, http://www.pluggable.co.uk
 * @author Sam Stephenson (Modified from Prototype Javascript framework)
 * @license MIT-style license @see http://www.prototypejs.org/
 */
//If native implementation is available, don't redefine it.
if (!Function.prototype.bind) {
    Function.prototype.bind = function(){
        // http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Functions:arguments
        if(arguments.length < 2 && (typeof arguments[0] == "undefined"))
            return this;
    
        var __method = this,
            args = Array.prototype.slice.call(arguments),
            object = args.shift();
    
        return function() {
            return __method.apply(object, args.concat(Array.prototype.slice.call(arguments)));
        };
    };
}
/**
 * FacesTypeAhead.js
 * @author Tim Finley
 * @author Ryan Silva
 * 
 * USAGE:
 * Just include FacesTypeAhead.js and QueryHelper.js on your page.  Add 
 * 
 * Implementation details: in essence, FacesTypeAhead is a static (literal) object which hooks up
 * input(s) to the TypeAhead plugin imported from Faces.  It initializes it and provides callbacks
 * for rendering results.  The hard work is done by TypeAhead, which is implemented farther along
 * in this file and not exposed outside this file's anonymous namespace wrapper.  It is mostly 
 * copied from Faces but modified to use QueryHelper.js, which abstracts away Dojo/JQuery so we 
 * can work with both.
 */
(function() {

var h = window.QueryHelper,
    protocol = '';

//If location.protocol is something else, like file:
//This won't happen in production so protocol will stay empty and thus relative
if (location.protocol != 'http:' && location.protocol != 'https:' ) {
    protocol = "https:";
}
 //

//Typeahead configuration defaults, mixes in with FacesTypeAhead.config.
var configDefaults = {

    minQueryLength: 2,
    searchDelay: 100,
    xhrTimeout: 20000,
    filterQueries: function(query) { return true; },
    showMoreResults: true, //show more results message when there are more results than shown
    resultsAlign: "right", //align results with which side of input box (left or right)
    sizeToInput: false, //when true, size result list with width of input box

    topsearch: {
        enabled: false,
        host: protocol + '//topsearch.ciolab.ibm.com',
        minScore: 0.2,
        maxResults: 4,
        headerLabel: '',     //Label for a header which would appear above the results
        iconUrl: protocol + "//w3.ibm.com/jct03001pt/w3ODWThemeSkin/themes/html/w3ODWTheme/images/favicon.ico",
        moreResultsUrl: protocol + "//w3.ibm.com/search/do/search?qt=${query}",
        moreResultsLabel: "See more w3 results",
        url: function(host) {
            return API.buildURL('/find/', host);
        },
        extraParams: {
            'limit': 13,
            'format': 'topsearch'
        },
        onclick: function(link) {
            location.href=link.url;
        } 
    },
    faces: {
        enabled: true,
        ariaLiveLabel: "There are ${count} suggested search results.  Use down and up arrow keys to navigate through the results.",
        prefixDom: null,    //Prefix the search results with a dom node
        minScore: 0,
        maxResults: 4,
        headerLabel:        '',     //Label for a header which would appear above the results
        moreResultsLabel:   'See more Faces (${count})',
        moreResultsLabel2:  'See more Faces (over ${count})',
        moreResultsUrl:     '/faces.tap.ibm.com#${query}',
        host:               protocol + '//faces.tap.ibm.com',
        imageHost:          protocol + '//images.tap.ibm.com',
        largeImageSize:     50,
        smallImageSize:     32,
        url: function(host) {
            return API.buildURL('/find/', host);
        },
        extraParams: {
            'threshold': 0,
            'limit': 13,
            'format': 'faces'
            //'user': 'finleyt@us.ibm.com',
        },
        onclick: function(person) {
            location.href=protocol + '//faces.tap.ibm.com/#uid:'+person.uid+'/'+person.name;
            //Alternative: opens Connections profile
            //location.href = 'http://w3.ibm.com/connections/profiles/html/profileView.do?email=' + person.email;
            //Or, return something to put it in the text box instead
            //return person.email;
        }
    }
};

var ESC_KEY = 27;
    
var me = window.FacesTypeAhead = {
    
    config: {},
    
    //Hook up the input to the typeahead control
    //Options:
    //init([domNode], [config])
    //config object mixes in with configDefaults.  See docs for format. Optional.
    //domNode is a reference to the dom node you want to hook up for the
    //    typeahead.  Optional
    init: function() {
        var userConfig={}, 
            config = {},
            domNode, 
            a = arguments,
            resultsID = uniqueID("typeahead-results");
        
        if ( a.length > 1 ) {
            domNode = a[0];
            userConfig = a[1];
        }
        else if ( a.length == 1 ) {
            if ( 'innerHTML' in a[0] ) 
                domNode = a[0];
            else
                userConfig = a[0];
        }
        //Make sure dom node is an array, if it wasn't
        if ( domNode && !domNode.length ) {
            domNode = [domNode];
        }
        //domNode wasn't passed in, so look for the inputs based on class name
        if ( !domNode ) {
            throw Error("DOM node missing from init call.");
        }

        if ( !userConfig.key ) {
            throw Error("API Key missing from config options.");
        }

        var features = {};  //Boolean map of features that are enabled for these typeahead instances

        //Put the configDefaults into config, but let user's settings take precedence
        //Later we stuff config into that typeahead instance
        h.extendObject(config, userConfig);

        for( var prop in configDefaults ) {
            if ( prop in userConfig) {
                //Extend config[prop] with what's in default configs
                //If it's not an object, let config override the default
                //(i.e., don't do anything)
                if ( typeof configDefaults[prop] == "object" ) {
                    config[prop] = h.extendObject({}, configDefaults[prop], userConfig[prop]);
                }
            }
            else {
                config[prop] = configDefaults[prop];
            }
            

            //Detect whether a feature has been enabled.
            //Features will be in the config object as an object with an enabled propery
            //like this:
            //config = {
            //  feature1: {
            //      enabled: true,
            //      foo: bar
            //  }
            //}
            if ( typeof config[prop] == 'object' && config[prop].enabled ) {
                features[prop] = true;
            }
        }

        config.features = features;
        
        //Set flags for which features are turned on
        //nodeFeatures is has keys as the dom nodes for each input, and the values
        //is an object with {feature1: true, feature2: true}
        var currNode;
        var typeaheads = new Array();
        
        for ( var i=0; i<domNode.length; i++ ) {
            currNode = h.queryAll(domNode[i]).attr("autocomplete", "off");
            typeaheads.push(new TypeAhead(currNode, config));
        }
        
        return typeaheads;
    }
};

//Helpers
function roughMagnitude(num) {
    if (num <= 100) {
        return num + '';
    } else if (num <= 150) {
        return '100+';
    } else if (num <= 200) {
        return '150+';
    } else {
        var str = num + '';
        return commaify((str.charAt(0) * 1) * Math.pow(10, str.length - 1)) + "+";
    }
}

function commaify(str) {
    str = str + '';
    str = str.split('').reverse().join('');
    str = str.replace(/(\d\d\d)/g, '$1,');
    str = str.split('').reverse().join('');
    
    if (str.charAt(0) == ',') {
        return str.substring(1);
    } else { 
        return str;
    }
}

//Return an array of the values in an object
//i.e., values({a: 'x', b: 'y'}) returns ['x', 'y']
function values(obj) {
    var values = new Array();
    for ( key in obj ) {
        if ( typeof obj[key] != 'function' ) {
            values.push(obj[key]);
        }
    }
    return values;
}

//Get a unique ID based on an input string
//for example, if you call uniqueID("foo")
//then it will return the first of foo, foo1,
//foo2, foo3, etc. which has not yet been taken.
function uniqueID(str) {
    var count = 0, id=str;
    while ( document.getElementById(id) )
        id = str + (count++);
    return id;
}

// Relevant bits of the Faces API
var API = {
    NUM_IMAGE_SERVERS: 3,
    PAGE_SIZE: 21,
    
    PersonResult: function(result) {
        h.extendObject(this, result['person']);
        
        this.score = result.score;
        
        for (var key in result) {
            if (result.hasOwnProperty(key) && key != 'person') {
                this[key] = result[key];
            }
        }
    },
        
    fixReturnedPerson: function(result) {
        return new this.PersonResult(result);
    },
    
    hashUIDToServer: function(uid) {
        if (uid) {
            var sum = 0;
            
            for (var i = 0, l = uid.length; i < l; i++) {
                sum += uid.charCodeAt(i);
            }
            
            return sum % API.NUM_IMAGE_SERVERS;
        } else {
            return 0;
        }
    },
        
    buildURL: function(str, host) {
        if (typeof host != "undefined") {
            return host + '/api' + str;
        } else if (/^\/faces/.test(window.location.pathname)) {
            return "/faces/api" + str;
        } else {
            return "/api" + str;
        }
    },
    
    MIN_DELTA_PERCENT: .09,
    MIN_DELTA_VALUE: 75,
    MIN_SCORE: 500,
    
    //RES: probably calculates deltas in the score to know when to show a couple of larger results
    calculateLargeDeltas: function(people, minDeltaPercent, minDeltaValue, minScore) {
        var maxScore = people[0].score,
            percentOfMax = [],
            deltas = [];
        
        minDeltaPercent = minDeltaPercent || API.MIN_DELTA_PERCENT;
        minDeltaValue = minDeltaValue || API.MIN_DELTA_VALUE;
        minScore = minScore || API.MIN_SCORE;
        
        for (var i = 0, l = people.length; i < l; i++) {
            var person = people[i],
                percentile = person.score/maxScore;
            
            percentOfMax.push(percentile);
            
            if (i > 0) {
                deltas.push(percentOfMax[i-1] - percentile);
            }
        }
        
        // console.log($.map(percentOfMax, function(x) { return Math.round(x * 100); }));
        // console.log($.map(people, function(x) { return x.score; }));
        // console.log($.map(deltas, function(x) { return Number((x * 100).toFixed(1)); }));

        var largeDeltas = h.map(deltas, function(x, i) {
            if (x >= minDeltaPercent && x * maxScore > minDeltaValue && people[i].score > minScore) {
                return { delta: x, end: i + 1};
            }
        }.bind(this)).sort(function(a, b) {
            if (a.delta < b.delta)
                return 1;
            else if (a.detla == b.delta)
                return 0;
            else
                return -1;
        });
        
        return largeDeltas;
    }
};

// Faces typeahead control, abstracted for dojo and jquery
var EMPTY_FUNCTION = function() {};

var TypeAhead = function (inputBox, settings) {
    var timer,
        resultsOffset,
        selectedClass = 'selected',
        lastResults,
        inProgressQuery,
        inProgressParams = {},
        initialValue,
        ajaxCounter = 0,
        ajaxCallsMap = {},
        ajaxCallsAbortedMap = {},
        inputToken,
        caches = {},    //Cache for ajax calls
        resultsID = uniqueID("typeahead-results"),
        KEY = {
            BACKSPACE: 8,
            RETURN: 13,
            SPACE: 32,
            LEFT: 37,
            UP: 38,
            RIGHT: 39,
            DOWN: 40,
            DELETE: 46,
            COMMA: 188
        },
        instanceAPI = {
            flushCache: function() {
                for ( var c in caches ) {
                    if ( caches.hasOwnProperty(c) ) {
                        caches[c].flush();
                    }
                }
            },
            
            execute: function(params, extraOptions) {
                settings.extraOptions = extraOptions || {};
                doSearch(getTypeaheadInputValue(), params);
            }
        }
    ;

    //Create caches
    for ( var f in settings.features ) {
        caches[f] = new TypeAhead.Cache();
    }

    //Add necessary ARIA attributes:
    this.inputBox = inputBox//.attr("role", "combobox")
           .attr("aria-autocomplete", "list")
           .attr("aria-owns", resultsID);

    //Expose settings
    this.settings = settings;

    //Create references to the template
    //Create a container for the drop-down
    this.container = h.queryAll(document.createElement('div'))
        .addClass('typeahead-results')
        .attr('id', resultsID)
        .attr('aria-expanded', 'false');
    
    h.changeCSS(this.container, {display: 'none'});
    document.body.appendChild(this.container[0]);     //do it better?

    if(settings.headerLabel||settings.headerHTML){
        var header = h.queryAll(document.createElement('h2'));
        header.addClass("typeahead-main-header");
        if(settings.headerLabel){
            h.setText(header, settings.headerLabel);
        }else{
            h.setHTML(header, settings.headerHTML);
        }
        header.appendTo(this.container);
    }
    
    //Create aria-live region
    //It will contain something like "showing 10 results"
    var ariaLive = document.createElement('div');
    ariaLive.style.display = 'none';
    ariaLive.setAttribute('aria-live', 'polite');
    this.container.append(ariaLive);

    var innerContainer = h.queryAll(document.createElement('div'));
    innerContainer.addClass("typeahead-inner-container");
    innerContainer.appendTo(this.container);

    //Add the customizable prefix section
    if ( settings.features.faces && settings.faces.prefixDom ) {
        innerContainer.append(settings.faces.prefixDom);
    }

    this.resultLists = {};
    this.resultHeaders = {};

    for ( var f in settings.features ) {
        var setting = settings[f];
        if ( setting.headerLabel || setting.description ) {
            var hcontainer = h.queryAll(document.createElement('div'));
            hcontainer.addClass("typeahead-result-header-container");
            hcontainer.addClass(f+"-result-header-container");
            if(setting.headerLabel){
                var header = h.queryAll(document.createElement('h3'));
                header.addClass("typeahead-result-header");
                h.setText(header, setting.headerLabel||"");
                header.appendTo(hcontainer);
            }
            if(setting.description){
                var description = h.queryAll(document.createElement('p'));
                description.addClass("typeahead-result-header-description");
                h.setText(description, setting.description||"")
                description.appendTo(hcontainer);
            }

            h.hide(hcontainer);
            this.resultHeaders[f] = hcontainer.appendTo(innerContainer);
        }
        this.resultLists[f] = 
            h.queryAll(document.createElement('ul')).addClass(f+'-typeahead-results').appendTo(innerContainer);
    }

    //////////////////////////
    //Returns true if the popup is currently open
    this.isPopupShowing = function() {
        return !h.isHidden(this.container);
    }

    var outputFunction = function(feature, query, resultsJson, pagingParams, settings) {
        resultsJson = resultsJson || {};

        if ( !feature ) {
            this.hideResults();
            return;
        }
        
        if (query == latestQuery) {
            try {
                renderResults(feature, resultsJson, query, pagingParams);
                // me.stopProgress();                    
            } catch (e) {
                // console.error("Typeahead error");
                // me.stopProgress();
                throw e;
            }
        } else {
            // Does me ever happen?
            //console.log('Old rendering canceled');
        }
    }.bind(this)

     var error = function () {
         //console.error("Typeahead error", arguments);
     }.bind(this);

     var onRequestStart = function(query) {
         // me.showProgress();
         latestQuery = query;
     }.bind(this);

     var onCachedOrEmptyRequest = function(query) {
         // me.showProgress();
         latestQuery = query;
     }.bind(this);
     
     var onIdentialQuery = function() {
         if (!this.isPopupShowing()) {
            positionAndShowResults();
        }    
     }.bind(this);
    
    var delayedSearch = function() {
        // Let the keyup/down event finish so the input value gets the new character
        setTimeout(function() {
            var query = getTypeaheadInputValue();
                
            if (isAcceptableQuery(query)) {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    doSearch(query);
                }, settings.searchDelay);
            }
        }, 0);
    };

    h.setData(inputBox, 'typeahead', instanceAPI);
    h.multiBind(inputBox, 'keyup keydown', function(event){
        var previousToken, nextToken, allTokens, i;
        
        if (event.type == 'keyup' && (event.keyCode == KEY.BACKSPACE || event.keyCode == KEY.DELETE)) {
            delayedSearch();
        } else if (event.type == 'keydown') {
            
            switch(event.keyCode) {
                case KEY.BACKSPACE:
                case KEY.DELETE:
                    //let keyupndle
                break;
                case KEY.RETURN:
                    if (this.isPopupShowing()) {
                        var curr = getCurrentSelection();
                        if ( curr ) {
                            event.preventDefault();
                            h.trigger("click", curr);
                        }
                    }
                    break;
                case KEY.UP:
                    if (this.isPopupShowing()) {
                        event.preventDefault();
                    }
                    highlightPrevious();
                    break;
                case KEY.DOWN:
                    if ( this.isPopupShowing()) {
                        highlightNext();
                    } else if ( inputBox.val().length ){
                        delayedSearch();
                    }
                    event.preventDefault();
                    break;
                default:
                    if (isPrintableCharacter(event.keyCode) || event.keyCode == KEY.SPACE) {
                        delayedSearch();
                    }
                break;
            }
        }
    }.bind(this));
    
    h.multiBind(this.container, "mouseover", function(event) {
        var t = event.target;
        //Get ancestor li
        while ( t != null && t != this.container && t.className.indexOf('result')==-1) {
            t = t.parentNode;
        }
        //If we stopped at an li that means we have the li.result node. 
        //Otherwise, do nothing.
        if ( t && t.nodeName.toLowerCase()=='li'
            && t.parentNode.className.indexOf("-typeahead-results") > -1 ) {
            var oldId;
            //If mousing over a different item than is already selected
            if (t.id && (oldId = h.getAttr(inputBox, 'aria-activedescendant')) != t.id ) {
                var old;
                if( oldId ){
                    //jquery doesn't like it if you query with an empty string
                    old = h.queryAll('#'+oldId);
                }
                _highlight(old, h.queryAll(t));
            }
        }
    }.bind(this));
    
    function getCurrentSelection() {
        var attr = h.getAttr(inputBox, 'aria-activedescendant'),
            curr;
        //hint: h.queryAll("#") is bad
        if ( attr ) {
            curr = h.queryAll("#" + attr);
            if ( curr.length > 0 ) {
                return curr;
            }
        }
        return null; 
    }
    
    /**
     * Return the first li before currItem
     * It assumes sequential lists, like:
     * <ul><li>a</li><li>b</li><li>c</li></ul>
     * <ul><li>1</li><li>2</li><li>3</li></ul>
     * If <li>2</li> is passed in, it returns a reference to <li>1</li>
     * If <li>1</li> is passed in, it returns a reference to <li>c</li>
     * If <li>a</li> is passed in, it returns null.
     * @param currItem list item reference
     * @returns reference to previous item
     */
    function getPreviousItem(currItem) {
        return getSequential(currItem, 'previousSibling');
    }
    
    /**
     * Return the next li after currItem
     * The opposite of getPreviousItem.
     */
    function getNextItem(currItem) {
        return getSequential(currItem, 'nextSibling');
    }
    /**
     * Helper function for getPreviousItem and getNextItem
     * @param currItem
     * @param prop either 'previousSibling' or 'nextSibling'
     * @returns the next/previous item
     */
    function getSequential(currItem, prop) {
        if ( currItem ) {
            //If a query object was passed in, unwrap it
            if ( currItem.length ) {
                currItem = currItem[0];
            }
            //Simple case: currItem.previousSibling/nextSibling exists, just return it
            if ( currItem[prop] ) {
                return h.queryAll(currItem[prop]);
            }
            //Otherwise, at the end/beginning of the list.  Move to next list.
            var children, 
                list = currItem.parentNode; //The <ul> element
            //Move through the sibling elements of the list element and find the next <ul> tag that's visible
            while ( (list = list[prop]) && list.nodeName.toUpperCase() != 'UL' && list.style.display != 'none' ) {}
            if ( list && list.nodeName.toUpperCase() == 'UL' && list.style.display != 'none' ) {
                children = list.childNodes;
                if ( children.length > 0 ) {
                    return h.queryAll(children[prop == 'nextSibling' ? 0 : children.length-1]);
                }
            }
        }
        return null;
    }
    
    var highlightNext = function() {
        if ( this.isPopupShowing() ) {
            var curr = getCurrentSelection(), next;

            if ( next = getNextItem(curr) ) {
                _highlight(curr, next);
            }
            //Highlight first item
            else if (curr == null) {
                //This should work but it doesn't, at least in dojo 1.3.  It returns all the li:first-child matches for all uls
                //_highlight(null, h.query(this.container, 'ul:nth-of-type(1) > li:first-child'));

                //Get the first child of the first ul which is neither empty nor hidden
                //
                //First, get the non-empty ULs
                var $ul = h.query(this.container, 'ul:not(ul:empty)');
                for ( var i=0; i<$ul.length; i++ ) { 
                    //If visible, highlight its first child
                    if ( $ul[i].style.display != 'none' ) {
                        _highlight(null, h.query(h.queryAll($ul[i]), 'li:first-child'));
                        break;
                    }
                }
            }
        }
    }.bind(this);
    
    var highlightPrevious = function() {
        if (this.isPopupShowing()){
            var curr = getCurrentSelection(), prev;
            if ( prev = getPreviousItem(curr) ) {
                _highlight(curr, prev);
            }
            //If we're at the beginning of the list, hide results
            else if ( curr ) {
                this.hideResults();
            }
        }
    }.bind(this);
    
    /**
     * helper function to highlight a new result from the drop-down
     * @param $curr query reference to the current selection
     * @param future dom node reference to the new selection
     */
    function _highlight($curr, $future) {
        if ( $curr ) { 
            $curr.removeClass(selectedClass); 
        }
        if ( $future && $future.length > 0 ) {
            $future.addClass(selectedClass);
            h.setAttr(inputBox, 'aria-activedescendant', $future[0].id);
        } else {
            h.setAttr(inputBox, 'aria-activedescendant', '');
        }
    }
    
    function getTypeaheadInputValue() {
        return h.trimString(h.getValue(inputBox).toLowerCase()).replace(/\s+/g, ' ');
    }
    
    function isAcceptableQuery(query) {
        return !query || settings.filterQueries(query);
    }
    
    function isPrintableCharacter(keycode) {
        /*   0-1a-z                              numpad 0-9 + - / * .                 ; = , - . / ^                         ( \ ) ' */
        if ((keycode >= 48 && keycode <= 90) || (keycode >= 96 && keycode <= 111) || (keycode >= 186 && keycode <= 192) || (keycode >= 219 && keycode <= 222)) {
            return true;
        }
    }

    // Populate the results dropdown with some results
    function outputHtml(feature, query, results, params) {
        var result = outputFunction(feature, query, results || [], params, settings);
        
        settings.extraOptions = null;
        return result;
    }
    
    function abortExistingAjaxCalls() {
        for (var k in ajaxCallsMap) {
            ajaxCallsAbortedMap[k] = true;
            ajaxCallsMap[k].abort();
        }
        ajaxCallsMap = {};
    }
    
    function identialToInProgressQuery(query, params) {

        if (inProgressQuery == query &&
            inProgressParams.idOffset == params.idOffset &&
            inProgressParams.scoreOffset == params.scoreOffset) {
            
            return true;
        } else {
            inProgressQuery = query;
            inProgressParams = params;
            
            return false;
        }
    }
    
    // Do a search
    var doSearch = function(query, params, timeout) {

        if ( this.disabled ) {
            return;
        }

        params = params || {};
        
        // Don't allow an identical query to be called twice in a row
        if (!identialToInProgressQuery(query, params)) {

            abortExistingAjaxCalls();
        
            if (query && query.replace(/\s/g, '').length >= settings.minQueryLength) {
                setTimeout(function(){
                    for ( var f in settings.features ) {
                        var cachedResults = caches[f].get(query),
                            data = {
                                'q': query,
                                key: settings.key
                            },
                            isPaging = false;
                    
                        if (params && params.idOffset && params.scoreOffset) {
                            data.idOffset = params.idOffset;
                            data.scoreOffset = params.scoreOffset;
                            isPaging = true;
                            data = h.extendObject(data, params);
                        }
                    
                        if (cachedResults && !isPaging) {
                            onCachedOrEmptyRequest(query);
                            outputHtml(f, query, cachedResults, params);
                        } else {
                            doAjaxRequest(f, query, params, data, isPaging);
                        }
                    }
                }, (timeout || 0));
            } else {
                onCachedOrEmptyRequest(query);
                outputHtml(null, query, [], params);
            }
        } else if (query) {
            onIdentialQuery();
        }
    }.bind(this);
    
    function doAjaxRequest(feature, query, params, data, isPaging) {
        
        onRequestStart(query, ajaxCounter);
       
        var xhr; // = {};
        var f = feature;

        xhr = h.fetchJSON({
            'url':      settings[f].url(settings[f].host),
            'data':     h.extendObject({}, settings[feature].extraParams, data),
            'timeout':  settings.xhrTimeout,
            'success':  function(xhrId) {
                return function (results, condition, xhr) {
                    // Don't let aborted requests succeed (not an exact measure of aborted requests though)
                    if ((!xhr || xhr.status == 200) && !ajaxCallsAbortedMap[xhrId]) {
                        lastResults = results;
                        
                        if (!isPaging) {
                            caches[f].add(query, results);
                        }
                        
                        outputHtml(f, query, results, params);
                    }
                    
                    delete ajaxCallsAbortedMap[xhrId];
                    delete ajaxCallsMap[xhrId];
                };
            }(ajaxCounter),
            
            'error': function (xhrId) {
                return function() {
                    settings.extraOptions = null;
                
                    if (settings.error) {
                        settings.error.apply(this, arguments);
                    }
                
                    delete ajaxCallsAbortedMap[xhrId];
                    delete ajaxCallsMap[xhrId];
                }; 
            }(ajaxCounter)
        });
        
        // xhr is null for some versions of ie
        if (xhr) {
            ajaxCallsMap[ajaxCounter++] = xhr;
        }
    }

    this.disable = function() {
        if ( arguments.length > 0 ) {
            if ( this.settings.features[arguments[0]] ) {
                this.settings.features[arguments[0]] = false;
            }
        }
        else {
            this.disabled = true;
        }
    }

    this.enable = function() {
        if ( arguments.length > 0 ) {
            this.settings.features[arguments[0]] = true;
        }
        else {
            this.disabled = false;
        }
    }

    var renderResults = function(feature, resultsJson, query) {
        //resultsJson will have results from each feature which is enabled
        //{ faces: { ... } , topsearch: { ... } }
        
        var results = [],
            numResults,
            listFragment = document.createDocumentFragment(),
            numTopResults,
            c = settings;
                
        //Hide the features that are disabled
        if ( !settings.features.faces ) {
            if ( this.resultLists.faces ) {
                h.hide(this.resultLists.faces);
            }
            if ( this.resultHeaders.faces ) {
                h.hide(this.resultHeaders.faces);
            }
        }
        if ( !settings.features.topsearch ) {
            if ( this.resultLists.topsearch ) {
                h.hide(this.resultLists.topsearch);
            }
            if ( this.resultHeaders.topsearch ) {
                h.hide(this.resultHeaders.topsearch);
            }
        }

        if ( settings.features.faces && feature == 'faces' ) {
            //Removed a section of code which set persons to an empty array if it
            //  didn't exist.  So just to be safe check to make sure persons array
            //  exists first.
            numResults = resultsJson.persons ? resultsJson.persons.length : 0;

            // Prepare results
            for (var i = 0; i < numResults; i++) {
                results.push( API.fixReturnedPerson(resultsJson.persons[i]) );
            }
            
            numTopResults = calculateNumberTopResults(results);
            
            // Don't show too many results when the top results take up twice as much space
            if (numTopResults * 2 + numResults - numTopResults >= 12) {
                results.splice(12 - numTopResults);
            }
            
            // Hack to only show results if score is high enough (only show good matches)
            if (c.faces.minScore && results.length > 0 && results[0].score < c.faces.minScore) {
                results = [];
            }
            
            if (c.faces.minScore && numTopResults > 0 && results[numTopResults] && results[numTopResults].score < c.faces.minScore) {
                results = results.slice(0, numTopResults);
            }
            
            // Hack to limit total results
            if (c.faces.maxResults) {
                
                if (c.faces.maxResults % 2 == 0 && numTopResults > 0) {
                    results = results.slice(0, c.faces.maxResults - 1);
                } else {
                    results = results.slice(0, c.faces.maxResults);
                }
            }
            
            if (results.length) {
                var moreResults;
                if(c.showMoreResults){
                    moreResults = getMoreResultsMessage(resultsJson, results.length, query);
                }
                for (var i = 0, l = results.length; i < l; i++) {
                    var result = results[i],
                        personDom = document.createElement('li'),
                        resultSize;
                    
                    if (i < numTopResults) {
                        resultSize = 'large-result';
                        personDom.className = 'result large-result';
                    }
                    else {
                        resultSize = 'small-result';
                        personDom.className = 'result small-result';
                    }
                    personDom.appendChild(buildPersonFragment(result, resultSize));
                    personDom.onclick = (function(person, that) { 
                        return function(e) { 
                            var retVal = c.faces.onclick(person);
                            //If the click handler returns a value, then put it in the input
                            if ( typeof retVal == "string" ) {
                                h.setValue(inputBox, retVal);
                                this.hideResults();
                                inputBox[0].focus();
                            }
                        }.bind(that);
                    })(result, this);
                    
                    personDom.id = uniqueID('faces-result-'+i);
                    
                    listFragment.appendChild(personDom);
                    
                    // Round the correct bottom corners
                    if (i >= results.length - 1 && (numTopResults + i) % 2 == 1) {
                        personDom.className += ' bottom-right';
                    } else if (i >= results.length - 2 && (numTopResults + i) % 2 == 0) {
                        personDom.className += ' bottom-left';
                    } 
                }
                if(c.showMoreResults && moreResults ){
                    moreResults.id = uniqueID('faces-more-results');
                    listFragment.appendChild(moreResults);
                }
                
                h.setHTML(this.resultLists.faces, listFragment);
                h.show(this.resultLists.faces); 
                if ( this.resultHeaders.faces ) {
                    h.show(this.resultHeaders.faces);
                }
            } else {
                if ( this.resultLists.faces ) {
                    h.hide(this.resultLists.faces);
                }
                if ( this.resultHeaders.faces ) {
                    h.hide(this.resultHeaders.faces);
                }
            }
        }
        
        var topQueries = [];
        
        if ( settings.features.topsearch && feature == 'topsearch' ) {
            resultsJson = resultsJson.pages;
            numResults = resultsJson.length;

            if (numResults > 0) {
                var resultsJsonFragment = document.createDocumentFragment();
                
                for (var i = 0; i < Math.min(resultsJson.length, settings.topsearch.maxResults); i++) {
                    var currentQuery = resultsJson[i].page,
                        link = document.createElement('a'),
                        li = document.createElement('li');
                    
                    link.href = currentQuery.url;
                    link.innerHTML = '<img src="' + settings.topsearch.iconUrl + '" class="icon" /><span class="query">' + currentQuery.title + '</span> ' + '<span class="url">' + currentQuery.url + '</span>';
                    li.appendChild(link);
                    li.id = uniqueID('topqueries-result-'+i);
                    li.className = 'result';
                    if(c.topsearch.onclick){
                        h.multiBind(h.queryAll(li), 'click', function(event){
                            if(event && event.preventDefault){
                                event.preventDefault();
                            }

                            var retVal = c.topsearch.onclick(currentQuery);
                            //If the click handler returns a value, then put it in the input
                            if ( typeof retVal == "string" ) {
                                h.setValue(inputBox, retVal);
                                this.hideResults();
                                inputBox[0].focus();
                            }
                        });
                    }
                    resultsJsonFragment.appendChild(li);
                }
                var moreResults = document.createElement('li');
                moreResults.className = "more-results";
                moreResults.innerHTML = '<a href="' +
                    settings.topsearch.moreResultsUrl.replace("${query}", encodeURIComponent(query))
                    + '">' + settings.topsearch.moreResultsLabel + '</a>';
                moreResults.id = uniqueID('topqueries-more-results');
                moreResults.onclick = function() {
                    location.href=settings.topsearch.moreResultsUrl.replace("${query}", encodeURIComponent(query));
                };
                resultsJsonFragment.appendChild(moreResults);
                
                h.setHTML(this.resultLists.topsearch, resultsJsonFragment);
                h.show(this.resultLists.topsearch);
                if ( this.resultHeaders.topsearch ) {
                    h.show(this.resultHeaders.topsearch);
                }
            } else {
                if ( this.resultLists.topsearch ) { 
                    h.hide(this.resultLists.topsearch);
                }
                if ( this.resultHeaders.topsearch ) {
                    h.hide(this.resultHeaders.topsearch);
                }
            }
        }

        //Only hide this feature, since other results might have come in for the same query
        if ( numResults == 0 ) { 
            this.hideResults(feature);
            var anyResults = false;
            //If there are no other results for any feature, hide the whole thing
            for ( var f in this.resultLists ) {
                if ( this.resultLists[f].length > 0 && this.resultLists[f][0].style.display != "none" ) {
                    anyResults = true;
                    break;
                }
            }
            if ( !anyResults ) {
                this.hideResults();
            }
                
        } else {
            positionAndShowResults();
        }
        
    }.bind(this);
    
    //Create a user item for the typeahead dropdown
    var buildPersonFragment = function(user, size) {
        var fragment = document.createDocumentFragment(),
            img = document.createElement('img'),
            info = document.createElement('div'),
            name = document.createElement('span'),
            meta = document.createElement('span'),
            email = document.createElement('span'),
            bio = document.createElement('span'),
            phone = document.createElement('span'),
            imageSize;
        
        if (size == 'large-result') {
            imageSize = settings.faces.largeImageSize;
        } else {
            imageSize = settings.faces.smallImageSize;
        }
        
        var port = 10000 + API.hashUIDToServer(user.uid);
        //Different ports for ssl.  If the current protocol is https, or facesHost specifies https
        if ( location.protocol == 'https:' || settings.faces.imageHost.indexOf('https:') == 0 ) {
            port += 10000;
        }
            
        img.src = [settings.faces.imageHost, ':', port, '/image/', user.uid, '.jpg?s=', imageSize].join('');
        img.setAttribute('alt', user.name);
        fragment.appendChild(img);
        
        name.appendChild(document.createTextNode(user.name));
        name.className = 'name';
        info.appendChild(name);
        info.appendChild(document.createElement('br'));
        
        meta.className = 'meta';
        info.appendChild(meta);
        
        if (user['office-phone'] || user['mobile-phone']) {
            phone.className = 'tel';
            
            if (user['office-phone']) {
                phone.innerHTML += [
                    '<span class="inner-label" style="display: none;"><span class="type">Office</span>: </span>',
                    '<span class="office-phone value">',
                        user['office-phone'],
                    '</span>'
                ].join('');
            }
        
            if (user['mobile-phone']) {
                phone.innerHTML += [
                    '<span class="inner-label"><span class="type" style="display: none;">Mobile</span>M: </span>',
                    '<span class="mobile-phone value">',
                        user['mobile-phone'],
                    '</span>'
                ].join('');
            }
            
            phone.appendChild(document.createElement('br'));
            meta.appendChild(phone);
        }
        
        email.appendChild(document.createTextNode(user.email));
        email.className = 'email';
        //email.href = 'mailto:' + user.email;
        meta.appendChild(email);
        meta.appendChild(document.createElement('br'));
        
        if (user.bio) {
            bio.className = 'bio';
            bio.appendChild(document.createTextNode(user.bio));
            meta.appendChild(bio);
        }
        
        fragment.appendChild(info);
        
        return fragment;
    }.bind(this);
    
    var getMoreResultsMessage = function(resultsJson, currentResults, query) {
        var totalMatches = resultsJson['matches'],
            onlyScopedToNetwork = resultsJson['network-only'];
        
        if (totalMatches > currentResults) {
            var roughString = roughMagnitude(totalMatches),
                moreResultsLabel;
            
            if (roughString.charAt(roughString.length - 1) == '+') {
                moreResultsLabel = settings.faces.moreResultsLabel2.replace("${count}", roughString.substring(0, roughString.length-1));
            }
            else {
                moreResultsLabel = settings.faces.moreResultsLabel.replace("${count}", roughString);
            } 
            
            var li = document.createElement('li');
            li.className = "more-results";
            
            li.innerHTML = '<a href="' + settings.faces.moreResultsUrl.replace("${query}", encodeURIComponent(query)) + '">'
                    + moreResultsLabel + '</a>';
                    
            //This gets called for keyboard navigation
            li.onclick = function() {
                location.href = settings.faces.moreResultsUrl.replace("${query}", encodeURIComponent(query))
            };
            
            return li;
        } else {
            return null;
        }
    }.bind(this);
    
    var calculateNumberTopResults = function(people) {
        if (people.length > 3) {
            var largeDeltas = API.calculateLargeDeltas(people);

            for (var i = 0, l = largeDeltas.length; i < l; i++) {
                var d = largeDeltas[i];

                if (d.end == 1 || d.end == 2) {
                    return d.end;
                }
            }

            return 0;
        } else {
            return people.length;
        }
    }.bind(this);
    
    var calculateResultsOffset = function () {
        var inputOffset = h.offset(inputBox);
        
        if (window.jQuery) {
            var inputOuterHeight = inputBox.outerHeight(),
                inputOuterWidth = inputBox.outerWidth();
        } else if (window.dojo) {
            var inputMarginBox = dojo.marginBox(inputBox[0]),
                inputOuterHeight = inputMarginBox.h,
                inputOuterWidth = inputMarginBox.w;
        }
        var resultsAreHidden = !this.isPopupShowing();
        // "Fake show" the results, since you get wrong info calling marginBox on hidden elements
        if (resultsAreHidden) {
            h.changeCSS(this.container, {'visibility': 'hidden'});
            h.show(this.container);
        }
        if(window.jQuery){
        var resultInnerWidth = this.container.innerWidth(),
                resultOuterWidth = this.container.outerWidth();
        }else{
            var resultOuterWidth = dojo.marginBox(this.container[0]).w;
            var resultInnerWidth = dojo.contentBox(this.container[0]).w;
        }
        //check for a min-width
        h.changeCSS(this.container, {"width":"0px"});
        if(window.jQuery){
            var newOuterWidth = this.container.outerWidth();
        }else{
            var newOuterWidth = dojo.marginBox(this.container[0]).w;
        }
        h.changeCSS(this.container, {"width":""});
            
        if (resultsAreHidden) {
            h.hide(this.container);
            h.changeCSS(this.container, {'visibility': ''});
        }
        var resultBorder = resultOuterWidth - resultInnerWidth;
        
        var offsets = {top: inputOffset.top + inputOuterHeight + 'px'};
        if(settings.resultsAlign=="left"){
            offsets.left= inputOffset.left+'px';
        }else if(settings.sizeToInput){
            //offset based on maximum possible width of results
            offsets.left= inputOffset.left - Math.max(newOuterWidth, inputOuterWidth) + inputOuterWidth + 'px';
        }else{
            offsets.left = inputOffset.left - resultOuterWidth + inputOuterWidth + 'px';
        }
        if(settings.sizeToInput){
            offsets.width= inputOuterWidth - resultBorder + 'px';
        }
        return offsets;
    }.bind(this);

    var positionAndShowResults = function() {
        resultsOffset = calculateResultsOffset();
        
        if (!this.isPopupShowing()) {
            setupHideEvents();
        }

        h.changeCSS(this.container, resultsOffset);
        var numResults = h.query(this.container, '.result').length;
        ariaLive.innerHTML = settings.faces.ariaLiveLabel.replace("${count}", numResults);
        
        h.show(this.container);
        this.container.attr('aria-expanded', 'true');
    }.bind(this);
    
    var setupHideEvents = function() {
        var body = h.queryAll('body');

        h.multiBind(inputBox, 'blur', function(event){
            //this needs to happen after a timeout because the blur may be the focus of chosen result
            //which is prevented if we hide the dropdown too early.
            window.setTimeout(function(){this.hideResults()}.bind(this), 200);
        }.bind(this));

        if (window.jQuery) {
            body.bind('click.faces-typeahead-hide', hideOnOutsideClick.bind(this));
            body.bind('keydown.faces-typeahead-hide', hideOnKeydown.bind(this));

        } else if (window.dojo) {
            this.hideOnOutsideClickHandle = dojo.connect(body[0], 'onclick', hideOnOutsideClick.bind(this));
            this.hideOnKeydownHandle = dojo.connect(body[0], 'onkeydown', hideOnKeydown.bind(this));            
        }
    }.bind(this);
    
    var hideOnOutsideClick = function(event) {
        if (event.target != inputBox[0] && !h.hasAncestor(h.queryAll(event.target), this.container)) {
            this.hideResults();
        }
    }.bind(this);
    
    var hideOnKeydown = function(event) {
        if (event.keyCode == ESC_KEY) {
            this.hideResults();
        }
    }.bind(this);


    
    this.hideResults = function(feature) {
        //hideAll set to false if we should only hide a feature, not the entire drop-down
        var hideAll = true;
       
        //only hide this feature
        if ( feature ) {
           var lists = h.queryAll(".typeahead-results > ul");
           var thisList = h.queryAll("." + feature + "-typeahead-results");
           //If there's more than one result type showing (or if the one we
           //  wanted to hide doesn't exist anyway) then don't hide the whole list.
           if ( thisList.length == 0 ) {
               return;
           }
           if ( lists.length > 1 ) {
               hideAll = false;
           }
       }
       if ( hideAll ) {
           if (window.jQuery) {
               $('body').unbind('.faces-typeahead-hide');
           } else if (window.dojo) {
               dojo.disconnect(this.hideOnOutsideClickHandle);
               dojo.disconnect(this.hideOnKeydownHandle);
               
                   delete this.hideOnOutsideClickHandle;
               delete this.hideOnKeydownHandle;
           } 
       }

        //Remove current selection, do this even if we're not hiding the whole list
        var attr = h.getAttr(inputBox, 'aria-activedescendant');
        if ( attr ) {
            h.queryAll("#"+attr).removeClass('selected');
            h.setAttr(inputBox, 'aria-activedescendant', '');
        }

        if ( hideAll ) {
            h.hide(this.container);
            this.container.attr('aria-expanded', 'false');
        }
    }.bind(this);
};

// Really basic cache for the results
TypeAhead.Cache = function (options) {
    var data = {},
        size = 0,
        settings = h.extendObject({
            MAX_SIZE: 10
        }, options)
    ;

    this.flush = function(){
        data = {};
        size = 0;
    };
    
    this.add = function (query, results) {
        if (size > settings.MAX_SIZE) {
            this.flush();
        }

        if (!data[query]) {
            size++;
        }

        data[query] = results;
    };

    this.get = function (query) {
        return data[query];
    };
};

})();   //end anonymous namespace wrapper
////////////////////////END odw-typeahead-prototype.js///////////////////////////////////////////
window.FacesTypeAhead.version='0.4.4';window.FacesTypeAhead.build='Wed Jun 26 10:24:14 EDT 2013';

