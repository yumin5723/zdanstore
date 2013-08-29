
function is_scalar(a) {
    return /string|number|boolean/.test(typeof a);
}

window.onloadRegister = function(hook) {
    window.loaded ? _runHook(hook) : _addHook('onloadhooks', hook);
};

function onafterloadRegister(hook) {
    window.afterloaded ? setTimeout(function () {
        _runHook(hook);
    }, 0) : _addHook('onafterloadhooks', hook);
}
function _onloadHook() {
    _runHooks('onloadhooks');
    window.loaded = true;
    Arbiter.inform('uipage_onload', true, Arbiter.BEHAVIOR_STATE);
}
function _onafterloadHook() {
    _runHooks('onafterloadhooks');
    window.afterloaded = true;
}
function _runHook(hook) {
    try {
        hook();
    } catch (e) {}
}
function _runHooks(hooks) {
    var e = hooks == 'onbeforeleavehooks' || hooks == 'onbeforeunloadhooks';
    var f = null;
    do {
        var b = window[hooks];
        if (!e) window[hooks] = null;
        if (!b) break;
        for (var d = 0; d < b.length; d++) try {
            if (e) {
                f = f || b[d]();
            } else b[d]();
        } catch (a) {}
        if (e) break;
    } while (window[hooks]);
    if (e && f) return f;
}

function keep_window_set_as_loaded() {
    if (window.loaded == false) {
        window.loaded = true;
        _runHooks('onloadhooks');
    }
    if (window.afterloaded == false) {
        window.afterloaded = true;
        _runHooks('onafterloadhooks');
    }
}
Arbiter.registerCallback(_onloadHook, [OnloadEvent.ONLOAD_DOMCONTENT_CALLBACK, InitialJSLoader.INITIAL_JS_READY]);
Arbiter.registerCallback(_onafterloadHook, [OnloadEvent.ONLOAD_DOMCONTENT_CALLBACK, OnloadEvent.ONLOAD_CALLBACK, InitialJSLoader.INITIAL_JS_READY]);
Arbiter.subscribe(OnloadEvent.ONBEFOREUNLOAD, function (b, a) {
    a.warn = _runHooks('onbeforeleavehooks') || _runHooks('onbeforeunloadhooks');
    if (!a.warn) {
        window.loaded = false;
        window.afterloaded = false;
    }
}, Arbiter.SUBSCRIBE_NEW);
Arbiter.subscribe(OnloadEvent.ONUNLOAD, function (b, a) {
    _runHooks('onunloadhooks');
}, Arbiter.SUBSCRIBE_NEW);

/**
 * chain callback functions
 */
function chain() {
    var ret_fn, fns = [];
    for (var i = 0; i < arguments.length; i++)
        fns.push(arguments[i]);
    ret_fn = function (event) {
        event = event || window.event;
        for (var f = 0; f < fns.length; f++)
            if (fns[f] && fns[f].apply(this, arguments) === false) {
            return false;
        } else if (event && event.cancelBubble) return true;
        return true;
    };
    ret_fn.toString = function () {
        return chain._toString(fns);
    };
    return ret_fn;
}

/**
 * chain._toString
 */
if (!chain._toString) chain._toString = function (b) {
    var d = 'chained fns',
        a = b.filter();
    for (var c = 0; c < a.length; c++) d += '\
' + a[c].toString();
    return d;
};

// void(0);

function HTML(content) {
    if (content && content.__html) content = content.__html;
    if (this === window) {
        if (content instanceof HTML) return content;
        return new HTML(content);
    }
    this._content = content;
    this._defer = false;
    this._extra_action = '';
    this._nodes = null;
    this._inline_js = bagofholding;
    this._has_option_elements = false;
    return this;
}
HTML.isHTML = function (content) {
    return content && (content instanceof HTML || content.__html !== undefined);
};
HTML.replaceJSONWrapper = function (json) {
    return json && json.__html !== undefined ? new HTML(json.__html) : json;
};
copy_properties(HTML.prototype, {
    toString: function () {
        var content = this._content || '';
        if (this._extra_action)
            content += '<script type="text/javascript">' + this._extra_action + '</script>';
        return content;
    },
    setAction: function (action) {
        this._extra_action = action;
        return this;
    },

    //get func run_action for run inline_jss and extra_action
    getAction: function () {
        this._fillCache();
        var run_action = function () {
            this._inline_js();
            eval_global(this._extra_action);
        }.bind(this);
        if (this.getDeferred()) {
            return run_action.defer.bind(run_action);
        } else {
            return run_action;
        }
    },
    setDeferred: function (y_or_n) {
        this._defer = !! y_or_n;
        return this;
    },
    getDeferred: function () {
        return this._defer;
    },
    getContent: function () {
        return this._content;
    },
    getNodes: function () {
        this._fillCache();
        return this._nodes;
    },
    getRootNode: function () {
        return this.getNodes()[0];
    },
    hasOptionElements: function () {
        this._fillCache();
        return this._has_option_elements;
    },
    _fillCache: function () {
        //fill nodes to this._nodes
        if (null !== this._nodes) return;
        var content = this._content;
        if (!content) {
            this._nodes = [];
            return;
        }
        //fix nodes can have content
        content = content.replace(/(<(\w+)[^>]*?)\/>/g, function (str, p1, p2) {
            return p2.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i) ? str : p1 + '></' + p2 + '>';
        });

        var new_content = $.trim(content).toLowerCase(),
            div = document.createElement('div'),
            b = false;
        //if content start with <opt / <leg ....., need wrapper 
        var wrapper = (!new_content.indexOf('<opt') && [1, '<select multiple="multiple" class="__WRAPPER">', '</select>']) ||
            (!new_content.indexOf('<leg') && [1, '<fieldset class="__WRAPPER">', '</fieldset>']) ||
            (new_content.match(/^<(thead|tbody|tfoot|colg|cap)/) && [1, '<table class="__WRAPPER">', '</table>']) ||
            (!new_content.indexOf('<tr') && [2, '<table><tbody class="__WRAPPER">', '</tbody></table>']) ||
            ((!new_content.indexOf('<td') || !new_content.indexOf('<th')) && [3, '<table><tbody><tr class="__WRAPPER">', '</tr></tbody></table>']) ||
            (!new_content.indexOf('<col') && [2, '<table><tbody></tbody><colgroup class="__WRAPPER">', '</colgroup></table>']) ||
            null;
        if (null === wrapper) {
            div.className = '__WRAPPER';
            if (ua.ie()) {
                wrapper = [0, '<span style="display:none">&nbsp;</span>', ''];
                add_span = true;
            } else wrapper = [0, '', ''];
        }
        //fill div content
        div.innerHTML = wrapper[1] + content + wrapper[2];
        //get origin node with one layer wrapper
        while (wrapper[0]--)
            div = div.lastChild;
        if (add_span)
            div.removeChild(div.firstChild);
        
        //div if the outer wrapper, div,select,filedset,table,tbody...
        // div.className != '__WRAPPER';
        if (0 != div.getElementsByTagName('option').length)
            this._has_option_elements = true;

        if (ua.ie()) {
            //remove added empty tbody for ie
            var i;
            if (!new_content.indexOf('<table') && -1 == new_content.indexOf('<tbody')) {
                i = div.firstChild && div.firstChild.childNodes;
            // } else if (wrapper[1] == '<table>' && -1 == new_content.indexOf('<tbody')) {
                // i = div.childNodes;
            } else i = [];
            for (var f = i.length - 1; f >= 0; --f)
                if (i[f].nodeName && i[f].nodeName.toLowerCase() == 'tbody' && i[f].childNodes.length == 0)
                    i[f].parentNode.removeChild(i[f]);
        }
        
        //filter the inline jss
        var scripts = div.getElementsByTagName('script');
        var jss = [];
        for (var e = 0; e < scripts.length; e++)
            if (scripts[e].src) {
                jss.push(Bootloader.requestResource.bind(Bootloader, 'js', scripts[e].src));
            } else { 
                jss.push(eval_global.bind(null, scripts[e].innerHTML)); 
            }
        for (var e = scripts.length - 1; e >= 0; e--)
            scripts[e].parentNode.removeChild(scripts[e]);
        var run_jss = function () {
            for (var l = 0; l < jss.length; l++)
                jss[l]();
        };
        this._inline_js = run_jss;

        this._nodes = to_array(div.childNodes);
    }
});

var DOM = {
    find: function (a, c) {
        var b = DOM.scry(a, c);
        return b[0];
    },
    scry: function (node, v) {
        if (!node) return [];
        var searchs = v.split(' ');
        var d = [node];
        var i = node === document;
        for (var m = 0; m < searchs.length; m++) {
            if (d.length == 0) break;
            if (searchs[m] == '') continue;
            var u = searchs[m];
            var s = [];
            var zd = false;
            if (u.charAt(0) == '^') if (m == 0) {
                zd = true;
                u = u.slice(1);
            } else
            return;
            u = u.replace(/\./g, ' .');
            u = u.replace(/\#/g, ' #');
            u = u.replace(/\[/g, ' [');
            var z = u.split(' ');
            var za = z[0] || '*';
            var n = z[1] && z[1].charAt(0) == '#';
            if (n) {
                var h = ge(z[1].slice(1), true);
                if (h && ('*' == za || h.tagName.toLowerCase() == za)) for (var q = 0; q < d.length; q++) if (zd && DOM.contains(h, d[q])) {
                    s = [h];
                    break;
                } else if (document == d[q] || DOM.contains(d[q], h)) {
                    s = [h];
                    break;
                }
            } else {
                var zc = [];
                var c = d.length;
                for (var o = 0; o < c; o++) {
                    if (zd) {
                        var k = [];
                        var g = d[o].parentNode;
                        var a = za == '*';
                        while (DOM.isNode(g, DOM.NODE_TYPES.ELEMENT)) {
                            if (a || g.tagName.toLowerCase() == za) k.push(g);
                            g = g.parentNode;
                        }
                    } else
                    var k = d[o].getElementsByTagName(za);
                    var l = k.length;
                    for (var r = 0; r < l; r++) zc.push(k[r]);
                }
                for (var x = 1; x < z.length; x++) {
                    var y = z[x];
                    var p = y.charAt(0) == '.';
                    var e = y.substring(1);
                    for (var o = 0; o < zc.length; o++) {
                        var zb = zc[o];
                        if (!zb) continue;
                        if (p) {
                            if (!CSS.hasClass(zb, e)) delete zc[o];
                            continue;
                        } else {
                            var f = y.slice(1, y.length - 1);
                            if (f.indexOf('=') == -1) {
                                if (zb.getAttribute(f) === null) {
                                    delete zc[o];
                                    continue;
                                }
                            } else {
                                var t = f.split('=');
                                var b = t[0];
                                var ze = t[1];
                                ze = ze.slice(1, ze.length - 1);
                                if (zb.getAttribute(b) != ze) {
                                    delete zc[o];
                                    continue;
                                }
                            }
                        }
                    }
                }
                for (var o = 0; o < zc.length; o++) if (zc[o]) {
                    s.push(zc[o]);
                    if (zd) break;
                }
            }
            d = s;
        }
        return d;
    },
    create: function (element, properties, content) {
        element = document.createElement(element);
        if (properties) {
            properties = copy_properties({}, properties);
            if (properties.style) {
                copy_properties(element.style, properties.style);
                delete properties.style;
            }
            // for (var p in properties)
            //     if (p.toLowerCase().indexOf('on') == 0) {
            //         if (!(typeof properties[p] != 'function'))
            //             if (window.Event && Event.listen) {
            //                 Event.listen(element, p.substr(2), properties[p]);
            //             } else element[p] = properties[p];
            //         delete properties[p];
            //     }
            copy_properties(element, properties);
        }
        if (content != undefined)
            DOM.setContent(element, content);
        return element;
    },
    getRootElement: function () {
        var a = null;
        return a || document.body;
    },
    isNode: function (obj, name_or_types) {
        if (typeof(Node) == 'undefined') {
            // for browser that has no "Node"
            Node = null;
        }

        try {
            //check obj instanceof Node  or has nodeName
            if (!obj ||
                !((Node != undefined && obj instanceof Node) || obj.nodeName))
                return false;
        } catch (ex) {
            return false;
        }
        if (typeof(name_or_types) !== 'undefined') {
            name_or_types = to_array(name_or_types).map(
                function (val) {
                    return (val + '').toUpperCase();
                });
            var node_name, node_type;
            try {
                node_name = new String(obj.nodeName).toUpperCase();
                node_type = obj.nodeType;
            } catch (ex) {
                return false;
            }
            for (var i = 0; i < name_or_types.length; i++) try {
                if (node_name == name_or_types[i] || node_type == name_or_types[i])
                    return true;
            } catch (ex) {}
            return false;
        }
        return true;
    },
    NODE_TYPES: {
        ELEMENT: 1,
        ATTRIBUTE: 2,
        TEXT: 3,
        CDATA_SECTION: 4,
        ENTITY_REFERENCE: 5,
        ENTITY: 6,
        PROCESSING_INSTRUCTION: 7,
        COMMENT: 8,
        DOCUMENT: 9,
        DOCUMENT_TYPE: 10,
        DOCUMENT_FRAGMENT: 11,
        NOTATION_NODE: 12
    },
    setContent: function (node, content) {
        if (!DOM.isNode(node))
            throw new Error('DOM.setContent: reference element is not a node');
        DOM.empty(node);
        return DOM.appendContent(node, content);
    },
    remove: function (node) {
        node = ge(node);
        if (node.parentNode)
            node.parentNode.removeChild(node);
    },
    empty: function (node) {
        node = ge(node);
        while (node.firstChild)
            DOM.remove(node.firstChild);
    },
    appendContent: function (node, content) {
        if (!DOM.isNode(node))
            throw new Error('DOM.appendContent: reference element is not a node');
        var fn = function (child) {
            node.appendChild(child);
        };
        return DOM._addContent(content, fn, node);
    },
    _addContent: function (content, fn_appendchild, node) {
        content = HTML.replaceJSONWrapper(content);
        if (content instanceof HTML && -1 == content.toString().indexOf('<script') && '' == node.innerHTML) {
            //content has no script and node is empty
            var ie_ver = ua.ie();
            if (!ie_ver || (ie_ver > 7 && !DOM.isNode(node, ['table', 'tbody', 'thead', 'tfoot', 'tr', 'select', 'fieldset']))) {
                node.innerHTML = content;
                return to_array(node.childNodes);
            }
        } else if (DOM.isNode(node, DOM.NODE_TYPES.TEXT)) {
            node.data = content;
            return [content];
        }

        var temp_node, e = [],
            actions = [];
        var child = document.createDocumentFragment();
        if (!(content instanceof Array))
            content = [content];
        for (var h = 0; h < content.length; h++) {
            temp_node = HTML.replaceJSONWrapper(content[h]);
            if (temp_node instanceof HTML) {
                actions.push(temp_node.getAction());
                var k = temp_node.getNodes(),
                    temp_child;
                for (var j = 0; j < k.length; j++) {
                    temp_child = (ua.safari() || (ua.ie() && temp_node.hasOptionElements())) ? k[j] : k[j].cloneNode(true);
                    e.push(temp_child);
                    child.appendChild(temp_child);
                }
            } else if (is_scalar(temp_node)) {
                var m = document.createTextNode(temp_node);
                e.push(m);
                child.appendChild(m);
            } else if (DOM.isNode(temp_node)) {
                e.push(temp_node);
                child.appendChild(temp_node);
            }
            // else if (!(temp_node instanceof Array)) temp_node !== null;
        }
        fn_appendchild(child);
        for (var h = 0; h < actions.length; h++)
            actions[h]();
        return e;
    }
};

function $N(el_type, properties, content) {
    if (typeof properties != 'object' || DOM.isNode(properties) || properties instanceof Array || HTML.isHTML(properties)) {
        content = properties;
        properties = null;
    }
    return DOM.create(el_type, properties, content);
}

function Vector2(x, y, domain) {
    copy_properties(this, {
        x: parseFloat(x),
        y: parseFloat(y),
        domain: domain || 'pure'
    });
}
copy_properties(Vector2.prototype, {
    toString: function () {
        return '(' + this.x + ', ' + this.y + ')';
    },
    add: function (c, d) {
        if (arguments.length == 1) {
            if (c.domain != 'pure')
                c = c.convertTo(this.domain);
            return this.add(c.x, c.y);
        }
        var x = parseFloat(c);
        var y = parseFloat(d);
        return new Vector2(this.x + x, this.y + y, this.domain);
    },
    mul: function (a, b) {
        if (typeof(b) == "undefined")
            b = a;
        return new Vector2(this.x * a, this.y * b, this.domain);
    },
    sub: function (a, b) {
        if (arguments.length == 1) {
            return this.add(a.mul(-1));
        } else
        return this.add(-a, -b);
    },
    distanceTo: function (a) {
        return this.sub(a).magnitude();
    },
    magnitude: function () {
        return Math.sqrt((this.x * this.x) + (this.y * this.y));
    },
    convertTo: function (domain) {
        if (domain != 'pure' && domain != 'viewport' && domain != 'document')
            return new Vector2(0, 0);
        if (domain == this.domain)
            return new Vector2(this.x, this.y, this.domain);
        if (domain == 'pure')
            return new Vector2(this.x, this.y);
        if (this.domain == 'pure')
            return new Vector2(0, 0);
        var scroll = Vector2.getScrollPosition('document');
        var x = this.x,
            y = this.y;
        if (this.domain == 'document') {
            x -= scroll.x;
            y -= scroll.y;
        } else {
            x += scroll.x;
            y += scroll.y;
        }
        return new Vector2(x, y, domain);
    },
    setElementPosition: function (a) {
        var b = this.convertTo('document');
        a.style.left = parseInt(b.x) + 'px';
        a.style.top = parseInt(b.y) + 'px';
        return this;
    },
    setElementDimensions: function (a) {
        return this.setElementWidth(a).setElementHeight(a);
    },
    setElementWidth: function (a) {
        a.style.width = parseInt(this.x, 10) + 'px';
        return this;
    },
    setElementHeight: function (a) {
        a.style.height = parseInt(this.y, 10) + 'px';
        return this;
    },
    scrollElementBy: function (a) {
        if (a == document.body) {
            window.scrollBy(this.x, this.y);
        } else {
            a.scrollLeft += this.x;
            a.scrollTop += this.y;
        }
        return this;
    }
});
copy_properties(Vector2, {
    getEventPosition: function (b, a) {
        a = a || 'document';
        b = $E(b);
        var d = b.pageX || (b.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
        var e = b.pageY || (b.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
        var c = new Vector2(d, e, 'document');
        return c.convertTo(a);
    },
    getScrollPosition: function (domain) {
        domain = domain || 'document';
        var left = document.body.scrollLeft || document.documentElement.scrollLeft;
        var top = document.body.scrollTop || document.documentElement.scrollTop;
        return new Vector2(left, top, 'document').convertTo(domain);
    },
    getElementPosition: function (c, b) {
        b = b || 'document';
        if (!c) return;
        if (!('getBoundingClientRect' in c))
            return new Vector2(0, 0, 'document');
        var e = c.getBoundingClientRect(),
            a = document.documentElement,
            d = Math.round(e.left) - a.clientLeft,
            f = Math.round(e.top) - a.clientTop;
        return new Vector2(d, f, 'viewport').convertTo(b);
    },
    getElementDimensions: function (a) {
        return new Vector2(a.offsetWidth || 0, a.offsetHeight || 0);
    },
    getViewportDimensions: function () {
        var a = (window && window.innerWidth) || (document && document.documentElement && document.documentElement.clientWidth) || (document && document.body && document.body.clientWidth) || 0;
        var b = (window && window.innerHeight) || (document && document.documentElement && document.documentElement.clientHeight) || (document && document.body && document.body.clientHeight) || 0;
        return new Vector2(a, b, 'viewport');
    },
    getDocumentDimensions: function () {
        var a = (document && document.documentElement && document.documentElement.scrollWidth) || (document && document.body && document.body.scrollWidth) || 0;
        var b = (document && document.documentElement && document.documentElement.scrollHeight) || (document && document.body && document.body.scrollHeight) || 0;
        return new Vector2(a, b, 'document');
    },
    scrollIntoView: function (a) {
        var b = a.offsetParent;
        var d = Rect(a);
        var c = d.boundWithin(Rect(b)).getPositionVector();
        d.getPositionVector().sub(c).scrollElementBy(b);
    }
});



var supportsBorderRadius = function () {
    var styles = ['KhtmlBorderRadius', 'OBorderRadius', 'MozBorderRadius', 'WebkitBorderRadius', 'msBorderRadius', 'borderRadius'];
    var ret = false,
    div = document.createElement('div');
    for (var i = styles.length - 1; i >= 0; i--)
        if (typeof(div.style[styles[i]]) != 'undefined') {
            ret = true;            
            break;
        }
    window.supportsBorderRadius = bagof(ret);
    return ret;
};

function URI(uri) {
    if (uri === window)
        return;
    if (this === window)
        return new URI(uri || window.location.href);
    this.parse(uri || '');
}
copy_properties(URI, {
    /**
     * get current uri
     */
    getRequestURI: function () {
        return new URI(window.location.href);
    },
    /**
     * uri parse regular expression
     */
    expression: /(((\w+):\/\/)([^\/:]*)(:(\d+))?)?([^#?]*)(\?([^#]*))?(#(.*))?/,
    /**
     * query parse expression
     * key[k1][k2]=value
     */
    arrayQueryExpression: /^(\w+)((?:\[\w*\])+)=?(.*)/,

    /**
     * explode query to object
     */
    explodeQuery: function (querys) {
        if (!querys) return {};
        var ret = {};
        querys = querys.replace(/%5B/ig, '[').replace(/%5D/ig, ']');
        querys = querys.split('&');
        for (var i = 0; i < querys.length; i++) {
            var query_arr = querys[i].match(URI.arrayQueryExpression);
            if (!query_arr) {
                //not has query[key]...
                var temp = querys[i].split('=');
                ret[URI.decodeComponent(temp[0])] = temp[1] === undefined ? null : URI.decodeComponent(temp[1]);
            } else {
                //query_arr[2] = "[k1][k2][k3]..."
                var keys = query_arr[2].split(/\]\[|\[|\]/).slice(0, -1);
                keys[0] = query_arr[1];
                var value = URI.decodeComponent(query_arr[3] || '');
                var temp = ret;
                for (var a = 0; a < keys.length - 1; a++)
                    if (keys[a]) {
                        if (temp[keys[a]] === undefined)
                            if (keys[a + 1] && !keys[a + 1].match(/\d+$/)) {
                                temp[keys[a]] = {};
                            } else temp[keys[a]] = [];
                        temp = temp[keys[a]];
                    } else {
                        if (keys[a + 1] && !keys[a + 1].match(/\d+$/)) {
                            temp.push({});
                        } else temp.push([]);
                        temp = temp[temp.length - 1];
                    }
                if (temp instanceof Array && keys[keys.length - 1] == '') {
                    temp.push(value);
                } else temp[keys[keys.length - 1]] = value;
            }
        }
        return ret;
    },
    /**
     * imploade query data s to query string
     */
    implodeQuery: function (query, key, need_encode) {
        key = key || '';
        if (need_encode === undefined)
            need_encode = true;
        var g = [];
        if (query === null || query === undefined) {
            // only push the query key
            g.push(need_encode ? URI.encodeComponent(key) : key);
        } else if (query instanceof Array) {
            //for query array [q1,q2,q3]
            for (var i = 0; i < query.length; ++i) try {
                if (query[i] !== undefined)
                    g.push(URI.implodeQuery(query[i], key ? (key + '[' + i + ']') : i));
            } catch (e) {}
        } else if (typeof query == 'object') {
            if (DOM.isNode(query)) {
                g.push('{node}');
            } else
            for (var k in query) try {
                if (query[k] !== undefined)
                    g.push(URI.implodeQuery(query[k], key ? (key + '[' + k + ']') : k));
            } catch (b) {}
        } else if (need_encode) {
            g.push(URI.encodeComponent(key) + '=' + URI.encodeComponent(query));
        } else g.push(key + '=' + query);
        return g.join('&');
    },
    encodeComponent: function (query_str) {
       return encodeURIComponent(query_str).replace(/%5D/g, "]").replace(/%5B/g, "[");
    },
    decodeComponent: function (query) {
        return window.decodeURIComponent(query.replace(/\+/g, ' '));
    }
});
copy_properties(URI.prototype, {
    /**
     * parse uri
     */
    parse: function (uri) {
        var a = uri.toString().match(URI.expression);
        copy_properties(this, {
            protocol: a[3] || '',
            domain: a[4] || '',
            port: a[6] || '',
            path: a[7] || '',
            query_s: a[9] || '',
            fragment: a[11] || ''
        });
        return this;
    },
    setProtocol: function (a) {
        this.protocol = a;
        return this;
    },
    getProtocol: function () {
        return this.protocol;
    },
    setQueryData: function (a) {
        this.query_s = URI.implodeQuery(a);
        return this;
    },
    addQueryData: function (data) {
        return this.setQueryData(copy_properties(this.getQueryData(), data));
    },
    removeQueryData: function (data) {
        if (!(data instanceof Array))
            data = [data];
        var old_data = this.getQueryData();
        for (var i = 0; i < data.length; ++i)
            delete old_data[data[i]];
        return this.setQueryData(old_data);
    },
    getQueryData: function () {
        return URI.explodeQuery(this.query_s);
    },
    setFragment: function (fragment) {
        this.fragment = fragment;
        return this;
    },
    getFragment: function () {
        return this.fragment;
    },
    setDomain: function (domain) {
        this.domain = domain;
        return this;
    },
    getDomain: function () {
        return this.domain;
    },
    setPort: function (port) {
        this.port = port;
        return this;
    },
    getPort: function () {
        return this.port;
    },
    setPath: function (path) {
        this.path = path;
        return this;
    },
    getPath: function () {
        return this.path.replace(/^\/+/, '/');
    },
    toString: function () {
        var a = '';
        this.protocol && (a += this.protocol + '://');
        this.domain && (a += this.domain);
        this.port && (a += ':' + this.port);
        if (this.domain && !this.path) a += '/';
        this.path && (a += this.path);
        this.query_s && (a += '?' + this.query_s);
        this.fragment && (a += '#' + this.fragment);
        return a;
    },
    valueOf: function () {
        return this.toString();
    },
    isMoofaURI: function () {
        if (!URI._moofaURIRegex) URI._moofaURIRegex = new RegExp('(^|\.)131\.(com)([^.]*)$', 'i');
        return (!this.domain || URI._moofaURIRegex.test(this.domain));
    },
    getRegisteredDomain: function () {
        if (!this.domain) return '';
        if (!this.isMoofaURI()) return null;
        var temp = this.domain.split('.');
        var key = temp.indexOf('moofa');
        return temp.slice(key).join('.');
    },
    getTld: function () {
        if (!this.domain) return '';
        var parts = this.domain.split('.');
        var last = parts[parts.length - 1];
        return last;
    },
    getUnqualifiedURI: function () {
        return new URI(this).setProtocol(null).setDomain(null).setPort(null);
    },
    getQualifiedURI: function () {
        var new_uri = new URI(this);
        if (!new_uri.getDomain()) {
            var current = URI();
            new_uri.setProtocol(current.getProtocol()).setDomain(current.getDomain()).setPort(current.getPort());
        }
        return new_uri;
    },
    isSameOrigin: function (origin) {
        var old = origin || window.location.href;
        if (!(old instanceof URI)) old = new URI(old.toString());
        if (this.getProtocol() && this.getProtocol() != old.getProtocol()) return false;
        if (this.getDomain() && this.getDomain() != old.getDomain()) return false;
        return true;
    },
    go: function () {
        goURI(this);
    },
    setSubdomain: function (subdomain) {
        var uri = new URI(this).getQualifiedURI();
        var temp = uri.getDomain().split('.');
        if (temp.length <= 2) {
            temp.unshift(subdomain);
        } else temp[0] = subdomain;
        return uri.setDomain(temp.join('.'));
    },
    getSubdomain: function () {
        if (!this.getDomain()) return '';
        var temp = this.getDomain().split('.');
        if (temp.length <= 2) {
            return '';
        } else
        return temp[0];
    },
    setSecure: function (secure) {
        return this.setProtocol(secure ? 'https' : 'http');
    },
    isSecure: function () {
        return this.getProtocol() == 'https';
    }
});

add_properties('Input', {
    isEmpty: function (a) {
        return !(/\S/).test(a.value || '') || CSS.hasClass(a, 'DOMControl_placeholder');
    },
    getValue: function (a) {
        return Input.isEmpty(a) ? '' : a.value;
    },
    setValue: function (a, b) {
        CSS.removeClass(a, 'DOMControl_placeholder');
        a.value = b;
    },
    setPlaceholder: function (a, b) {
        a.setAttribute('title', b);
        a.setAttribute('placeholder', b);
        if (Input.isEmpty(a)) {
            CSS.addClass(a, 'DOMControl_placeholder');
            a.value = b;
        }
    },
    reset: function (a) {
        Input.setValue(a, '');
        var b = a.getAttribute('placeholder');
        b && Input.setPlaceholder(a, b);
        a.style.height = '';
    },
    setSubmitOnEnter: function (a, b) {
        CSS.conditionClass(a, 'enter_submit', b);
    },
    getSubmitOnEnter: function (a) {
        return CSS.hasClass(a, 'enter_submit');
    }
});

var ErrorDialog = {
    showAsyncError: function(error) {
        try {
            return ErrorDialog.show(error.getErrorSummary(), error.getErrorDescription());
        } catch (error) {
            alert(error);
        }
    },
    show: function(summary, description) {
        return (new Dialog()).setTitle(summary).setBody(description).setButtons([Dialog.OK]).setStackable(true).setClassName('errorDialog').setModal(true).setHandler(bagofholding).show();
    }
};

add_properties('Form', {
    getInputs: function (form_object) {
        form_object = form_object || document;
        //search for input/select/texteara/button
        return [].concat(to_array(DOM.scry(form_object, 'input')), to_array(DOM.scry(form_object, 'select')), to_array(DOM.scry(form_object, 'textarea')), to_array(DOM.scry(form_object, 'button')));
    },
    getSelectValue: function (select) {
        return select.options[select.selectedIndex].value;
    },
    setSelectValue: function (select, value) {
        for (var i = 0; i < select.options.length; ++i)
            if (select.options[i].value == value) {
                select.selectedIndex = i;
                break;
            }
    },
    getRadioValue: function (radio) {
        for (var i = 0; i < radio.length; i++)
            if (radio[i].checked)
                return radio[i].value;
        return null;
    },
    getElements: function (elmt) {
        return to_array(elmt.tagName == 'FORM' ? elmt.elements : Form.getInputs(elmt));
    },
    getAttribute: function (elmt, attr) {
        return (elmt.getAttributeNode(attr) || {}).value || null;
    },
    setDisabled: function (form, disabled) {
        Form.getElements(form).forEach(function (c) {
            if (c.disabled != undefined) {
                var d = DataStore.get(c, 'origDisabledState');
                if (disabled) {
                    if (d === undefined) DataStore.set(c, 'origDisabledState', c.disabled);
                    c.disabled = disabled;
                } else {
                    if (d !== true) c.disabled = false;
                    DataStore.remove(c, 'origDisabledState');
                }
            }
        });
    },
    bootstrap: function (c, d) {
        var e = (Form.getAttribute(c, 'method') || 'GET').toUpperCase();
        d = Parent.byTag(d, 'button') || d;
        var f = DOMPath.findNodePath(c);
        var h = Parent.byClass(d, 'stat_elem') || c;
        if (CSS.hasClass(h, 'async_saving')) return;
        var b = Form.serialize(c, d);
        Form.setDisabled(c, true);
        var a = Form.getAttribute(c, 'ajaxify') || Form.getAttribute(c, 'action');
        var g = new AsyncRequest(a);
        g.setData(b).setNectarModuleDataSafe(c).setReadOnly(e == 'GET').setMethod(e).setRelativeTo(c).setStatusElement(h).setHandler(function (i) {
            if (i.isReplay()) g.setRelativeTo(DOMPath.resolveNodePath(f));
        }).setFinallyHandler(Form.setDisabled.bind(null, c, false)).send();
    },
    serialize: function (b, c) {
        var a = {};
        Form.getElements(b).forEach(function (d) {
            if (d.name && !d.disabled && d.type != 'submit') if (!d.type || ((d.type == 'radio' || d.type == 'checkbox') && d.checked) || d.type == 'text' || d.type == 'password' || d.type == 'hidden' || d.tagName == 'TEXTAREA') {
                Form._serializeHelper(a, d.name, Input.getValue(d));
            } else if (d.tagName == 'SELECT') for (var e = 0, f = d.options.length; e < f; ++e) {
                var g = d.options[e];
                if (g.selected) Form._serializeHelper(a, d.name, g.value);
            }
        });
        if (c && c.name && 'submit' == c.type && DOM.contains(b, c) && DOM.isNode(c, ['input', 'button'])) Form._serializeHelper(a, c.name, c.value);
        return Form._serializeFix(a);
    },
    _serializeHelper: function (a, d, e) {
        var c = /([^\]]+)\[([^\]]*)\](.*)/.exec(d);
        if (c) {
            a[c[1]] = a[c[1]] || {};
            if (c[2] == '') {
                var b = 0;
                while (a[c[1]][b] != undefined) b++;
            } else b = c[2];
            if (c[3] == '') {
                a[c[1]][b] = e;
            } else Form._serializeHelper(a[c[1]], b.concat(c[3]), e);
        } else a[d] = e;
    },
    _serializeFix: function (a) {
        var e = [];
        for (var b in a) {
            if (a[b] instanceof Object) {
                a[b] = Form._serializeFix(a[b]);
            }
            e.push(b);
        }
        var d = 0,
            c = true;
        e.sort().each(function (g) {
            if (g != d++) c = false;
        });
        if (c) {
            var f = {};
            e.each(function (g) {
                f[g] = a[g];
            });
            return f;
        } else
        return a;
    },
    post: function (d, b, c) {
        var a = document.createElement('form');
        a.action = d.toString();
        a.method = 'POST';
        a.style.display = 'none';
        if (c) a.target = c;
        if (ge('MF_CSRF_TOKEN')) b.MF_CSRF_TOKEN = ge('MF_CSRF_TOKEN').value;
        b.fb_dtsg = Env.fb_dtsg;
        b.post_form_id_source = 'dynamic_post';
        b.next = htmlspecialchars(document.location.href);
        Form.createHiddenInputs(b, a);
        DOM.getRootElement().appendChild(a);
        a.submit();
        return false;
    },
    createHiddenInputs: function (g, a, d, f) {
        d = d || {};
        var c;
        var h = URI.implodeQuery(g, '', false);
        var i = h.split('&');
        for (var b = 0; b < i.length; b++) if (i[b]) {
            var j = i[b].split('=');
            var e = j[0];
            var k = j[1];
            if (e === undefined || k === undefined) continue;
            k = URI.decodeComponent(k);
            if (d[e] && f) {
                d[e].value = k;
            } else {
                c = $N('input', {
                    type: 'hidden',
                    name: e,
                    value: k
                });
                d[e] = c;
                a.appendChild(c);
            }
        }
        return d;
    },
    getFirstElement: function (b) {
        var f = ['input[type="text"]', 'textarea', 'input[type="password"]', 'input[type="button"]', 'input[type="submit"]'];
        var e = [];
        for (var c = 0; c < f.length && e.length == 0; c++) e = DOM.scry(b, f[c]);
        if (e.length > 0) {
            var d = e[0];
            try {
                if (elementY(d) > 0 && elementX(d) > 0) return d;
            } catch (a) {}
        }
        return null;
    },
    focusFirst: function (b) {
        var a = Form.getFirstElement(b);
        if (a) {
            a.focus();
            return true;
        }
        return false;
    }
});

/**
 * Dialog
 */
function Dialog(model) {
    this._show_loading = true;
    this._loading_text = null;
    this._loading_was_shown = false;
    this._auto_focus = true;
    this._fade_enabled = true;
    this._onload_handlers = [];
    this._top = 140;
    this._uniqueID = 'dialog_' + Dialog._globalCount++;
    this._content = null;
    this._obj = null;
    this._popup = null;
    this._overlay = null;
    this._hidden_objects = [];
    if (model) this._setFromModel(model);
}
copy_properties(Dialog, {
    OK: {
        name: 'ok',
        label: "Okay"
    },
    CANCEL: {
        name: 'cancel',
        label: "Cancel",
        className: 'inputaux'
    },
    CLOSE: {
        name: 'close',
        label: "Close"
    },
    NEXT: {
        name: 'next',
        label: "Next"
    },
    SAVE: {
        name: 'save',
        label: "Save"
    },
    SUBMIT: {
        name: 'submit',
        label: "Submit"
    },
    CONFIRM: {
        name: 'confirm',
        label: "Confirm"
    },
    DELETE: {
        name: 'delete',
        label: "Delete"
    },
    _globalCount: 0,
    _bottoms: [0],
    max_bottom: 0,
    _updateMaxBottom: function () {
        Dialog.max_bottom = Math.max.apply(Math, Dialog._bottoms);
    }
});
copy_properties(Dialog, {
    //only windows can use objects
    SHOULD_HIDE_OBJECTS: !ua.windows(),
    OK_AND_CANCEL: [Dialog.OK, Dialog.CANCEL],
    _STANDARD_BUTTONS: [Dialog.OK, Dialog.CANCEL, Dialog.CLOSE, Dialog.SAVE, Dialog.SUBMIT, Dialog.CONFIRM, Dialog.DELETE],
    _useCSSBorders: window.supportsBorderRadius() || ua.ie() > 6,
    SIZE: {
        WIDE: 500,
        STANDARD: 445
    },
    _HALO_WIDTH: 10,
    _BORDER_WIDTH: 1,
    _PADDING_WIDTH: 10,
    MODALITY: {
        DARK: 'dark',
        WHITE: 'white'
    },
    dialogStack: null,
    newButton: function (name, label, classname, handler) {
        var btn = {
            name: name,
            label: label
        };
        if (classname) btn.className = classname;
        if (handler) btn.handler = handler;
        return btn;
    },
    //get current (last one) dialog
    getCurrent: function () {
        var stack = Dialog.dialogStack;
        if (!stack || !stack.length) return null;
        return stack[stack.length - 1];
    },
    //quick boot a dialog
    bootstrap: function (uri, query_data, read_only, method, model, status) {
        query_data = query_data || {};
        copy_properties(query_data, new URI(uri).getQueryData());
        method = method || (read_only ? 'GET' : 'POST');
        var status_elmt = Parent.byClass(status, 'stat_elem') || status;
        if (status_elmt && CSS.hasClass(status_elmt, 'async_saving'))
            return false;
        var request = new AsyncRequest().setReadOnly(!!read_only).setMethod(method).setRelativeTo(status).setStatusClass(status_elmt);
        var dialog = new Dialog(model).setAsync(request.setURI(uri).setData(query_data));
        dialog.show();
        return false;
    },
    //set properties
    _basicMutator: function (name) {
        return function (value) {
            this[name] = value;
            this._dirty();
            return this;
        };
    },
    //find button use name
    _findButton: function (btns, name) {
        if (btns)
            for (var i = 0; i < btns.length; ++i)
                if (btns[i].name == name)
                    return btns[i];
        return null;
    },
    // _keyDownFilter: function (event, a) {
    //     return a == 'onkeydown' && KeyEventController.filterEventModifiers(event, a);
    // },

    //close all dialog
    _tearDown: function () {
        Dialog._hideAll();
        Dialog.dialogStack = null;
    },
    /**
     * hide all dialog in stack
     */
    _hideAll: function () {
        if (Dialog.dialogStack !== null && Dialog.dialogStack.length) {
            var stack = Dialog.dialogStack.clone();
            Dialog.dialogStack = null;
            for (var i = stack.length - 1; i >= 0; i--)
                stack[i].hide();
        }
    },
    // _handleEscapeKey: function (event, a) {
    //     Dialog._escape();
    // },
    _escape: function () {
        var dialog = Dialog.getCurrent();
        if (!dialog) return true;
        var semi_modal = dialog._semi_modal;
        var btns = dialog._buttons;

        //no semi-modal,no buttons,do nothing
        if (!btns && !semi_modal)
            return true;
        //just hide no button but semi-modal dialog
        if (semi_modal && !btns) {
            dialog.hide();
            return false;
        }

        //call cancelHandler or cancel button or the only button
        var btn;
        var cancel_btn = Dialog._findButton(btns, 'cancel');
        if (dialog._cancelHandler) {
            //has cancelHandler, call cancel()
            dialog.cancel();
            return false;
        } else if (cancel_btn) {
            btn = cancel_btn;
        } else if (btns.length == 1) {
            btn = btns[0];
        } else
            return true;
        dialog._handleButton(btn);
        return false;
    },
    //call func with obj as context and args as params
    call_or_eval: function (obj, func, args) {
        if (!func) return undefined;
        args = args || {};
        if (typeof(func) == 'string') {
            var params = keys(args).join(', ');
            func = eval('({f: function(' + params + ') { ' + func + '}})').f;
        }
        return func.apply(obj, values(args));
    }
});
copy_properties(Dialog.prototype, {
    /**
     * dialog show
     */
    show: function (modal) {
        this._showing = true;
        if (modal) {
            if (this._overlay) this._overlay.style.display = '';
            if (this._fade_enabled) CSS.setStyle(this._obj, 'opacity', 1);
            this._obj.style.display = '';
        } else this._dirty();
        return this;
    },

    /**
     * show loading dialog
     */
    showLoading: function () {
        this._loading_was_shown = true;
        this._renderDialog($N('div', {
            className: 'dialog_loading'
        }, this._loading_text || "Loading..."));
        return this;
    },
    
    /**
     * hide dialog
     */
    hide: function (modal) {
        if (!this._showing) return this;
        this._showing = false;
        if (this._autohide_timeout) {
            clearTimeout(this._autohide_timeout);
            this._autohide_timeout = null;
        }
        if (this._fade_enabled && (!Dialog.dialogStack || Dialog.dialogStack.length <= 1)) {
            this._fadeOut(modal);
        } else this._hide(modal);
        return this;
    },
    /**
     * cancel dialog
     */
    cancel: function () {
        if (!this._cancelHandler || this._cancelHandler() !== false) this.hide();
    },

    /**
     * get dialog root object
     */
    getRoot: function () {
        return this._obj;
    },
    /**
     * get dialog body
     */
    getBody: function () {
        return DOM.scry(this._obj, 'div.dialog_body')[0];
    },

    /**
     * get button 
     */
    getButtonElement: function (btn) {
        if (typeof btn == 'string')
            btn = Dialog._findButton(this._buttons, btn);
        if (!btn || !btn.name) return null;

        var buttons = DOM.scry(this._popup, 'input');
        var fn = function (b) {
            return b.name == btn.name;
        };
        return buttons.filter(fn)[0] || null;
    },

    /**
     * get dialog content node
     */
    getContentNode: function () {
        var contents = DOM.scry(this._content, 'div.dialog_content');
        // a.length != 1;
        return contents[0];
    },

    getFormData: function () {
        return Form.serialize(this.getContentNode());
    },
    setShowing: function () {
        this.show();
        return this;
    },
    setHiding: function () {
        this.hide();
        return this;
    },
    setTitle: Dialog._basicMutator('_title'),
    setBody: Dialog._basicMutator('_body'),
    setExtraData: Dialog._basicMutator('_extra_data'),
    setReturnData: Dialog._basicMutator('_return_data'),
    setShowLoading: Dialog._basicMutator('_show_loading'),
    setLoadingText: Dialog._basicMutator('_loading_text'),
    setFullBleed: Dialog._basicMutator('_full_bleed'),
    setImmediateRendering: function (y_or_n) {
        this._immediate_rendering = y_or_n;
        return this;
    },
    setUserData: Dialog._basicMutator('_user_data'),
    getUserData: function () {
        return this._user_data;
    },
    setAutohide: function (a) {
        if (a) {
            if (this._showing) {
                this._autohide_timeout = setTimeout(this.hide.shield(this), a);
            } else this._autohide = a;
        } else {
            this._autohide = null;
            if (this._autohide_timeout) {
                clearTimeout(this._autohide_timeout);
                this._autohide_timeout = null;
            }
        }
        return this;
    },
    setSummary: Dialog._basicMutator('_summary'),
    setButtons: function (a) {
        var c;
        if (!(a instanceof Array)) {
            c = to_array(arguments);
        } else c = a;
        for (var d = 0; d < c.length; ++d) if (typeof c[d] == 'string') {
            var b = Dialog._findButton(Dialog._STANDARD_BUTTONS, c[d]);
            !b;
            c[d] = b;
        }
        this._buttons = c;
        this._updateButtons();
        return this;
    },
    setButtonsMessage: Dialog._basicMutator('_buttons_message'),
    setClickButtonOnEnter: function (b, a) {
        this._clickButtonOnEnter = a;
        this._clickButtonOnEnterInputName = b;
        return this;
    },
    setStackable: function (b, a) {
        this._is_stackable = b;
        this._shown_while_stacked = b && a;
        return this;
    },
    setHandler: function (a) {
        this._handler = a;
        return this;
    },
    setCancelHandler: function (a) {
        this._cancelHandler = Dialog.call_or_eval.bind(null, this, a);
        return this;
    },
    setCloseHandler: function (a) {
        this._close_handler = Dialog.call_or_eval.bind(null, this, a);
        return this;
    },
    clearHandler: function () {
        return this.setHandler(null);
    },
    setPostURI: function (b, a) {
        if (a === undefined) a = true;
        if (a) {
            this.setHandler(this._submitForm.bind(this, 'POST', b));
        } else this.setHandler(function () {
            Form.post(b, this.getFormData());
            this.hide();
        }.bind(this));
        return this;
    },
    setGetURI: function (a) {
        this.setHandler(this._submitForm.bind(this, 'GET', a));
        return this;
    },
    setModal: function (a, b) {
        if (a === undefined) a = true;
        this._showing && this._modal && !a;
        if (a && b) switch (b) {
        case Dialog.MODALITY.DARK:
            this._modal_class = 'dark';
            break;
        case Dialog.MODALITY.WHITE:
            this._modal_class = 'white';
            break;
        }
        this._modal = a;
        return this;
    },
    setSemiModal: function (a) {
        if (a === undefined) a = true;
        if (a) this.setModal(true, Dialog.MODALITY.DARK);
        this._semi_modal = a;
        return this;
    },
    setWideDialog: Dialog._basicMutator('_wide_dialog'),
    setContentWidth: Dialog._basicMutator('_content_width'),
    setTitleLoading: function (b) {
        if (b === undefined) b = true;
        var a = DOM.find(this._popup, 'h2.dialog_title');
        if (a) CSS.conditionClass(a, 'loading', b);
        return this;
    },
    setSecure: Dialog._basicMutator('_secure'),
    setClassName: Dialog._basicMutator('_class_name'),
    setFading: Dialog._basicMutator('_fade_enabled'),
    setFooter: Dialog._basicMutator('_footer'),
    setAutoFocus: Dialog._basicMutator('_auto_focus'),
    setTop: Dialog._basicMutator('_top'),
    onloadRegister: function (a) {
        to_array(a).forEach(function (b) {
            if (typeof b == 'string') b = new Function(b);
            this._onload_handlers.push(b.bind(this));
        }.bind(this));
        return this;
    },
    setAsyncURL: function (a) {
        return this.setAsync(new AsyncRequest(a));
    },
    setAsync: function (request) {
        var c = function (response) {
            if (this._async_request != request) return;
            this._async_request = null;
            var payload = response.getPayload();
            var model = payload;
            var update = function() {
                if (typeof model == "string") {
                    this.setBody(model);
                } else this._setFromModel(model);
                this._update(true);
            }.bind(this);
            update();
        }.bind(this);
        // var b = request.getData();
        // b.__d = 1;
        // a.setData(b);
        var d = bind(this, 'hide');
        request.setHandler(chain(request.getHandler(), c)).setErrorHandler(chain(d, request.getErrorHandler())).setTransportErrorHandler(chain(d, request.getTransportErrorHandler())).send();
        this._async_request = request;
        this._dirty();
        return this;
    },
    _dirty: function () {
        //_is_dirty mean need update
        if (!this._is_dirty) {
            this._is_dirty = true;
            if (this._immediate_rendering) {
                this._update();
            } else bind(this, '_update', false).defer();
        }
    },
    _format: function (content) {
        if (typeof content == 'string') {
            content = HTML(content);
        } else content = HTML.replaceJSONWrapper(content);
        if (content instanceof HTML)
            content.setDeferred(true);
        return content;
    },
    _update: function (force) {
        if (!this._is_dirty && force !== true) return;
        this._is_dirty = false;
        if (!this._showing) return;
        //set autohide
        if (this._autohide && !this._async_request && !this._autohide_timeout)
            this._autohide_timeout = setTimeout(bind(this, 'hide'), this._autohide);

        //noloading or not show loading
        if (!this._async_request || !this._show_loading) {
            //hide loading if was shown
            if (this._loading_was_shown === true) {
                this._hide(true);
                this._loading_was_shown = false;
            }

            /**
             * render content
             */
            var content = [];
            //summary
            if (this._summary)
                content.push($N('div', {className: 'dialog_summary'}, this._format(this._summary)));
            //body
            var body = $N('div', {className: 'dialog_body'}, this._format(this._body));
            if (window.traversal) {
                window.traversal(body, this);
            }
            content.push(body);

            //add buttons
            var btns = this._getButtonContent();
            if (btns.length) content.push($N('div', {
                className: 'dialog_buttons clearfix'
            }, btns));
            
            //add footer
            if (this._footer) content.push($N('div', {
                className: 'dialog_footer'
            }, this._format(this._footer)));
            
            //add wrapper
            content = $N('div', {
                className: 'dialog_content'
            }, content);

            if (this._title) {
                var title_span = $N('span', this._format(this._title));
                var close_button = $N('a',{
                    id:'colose_windowbutton',
                    className :'close_windowbutton',
                    href:'javascript:closeDialog();'
                },'X');
                var title = $N('h2', {
                    className: 'dialog_title',
                    id: 'title_' + this._uniqueID
                }, title_span);
                
                CSS.conditionClass(title, 'secure', this._secure);
                content = [title,close_button,content];
            } else content = [content];
            this._renderDialog(content);
            CSS.conditionClass(this.getRoot(), 'omitDialogFooter', !btns.length);

            //bind button press event
            if (this._clickButtonOnEnterInputName && this._clickButtonOnEnter && ge(this._clickButtonOnEnterInputName))
                Event.listen(ge(this._clickButtonOnEnterInputName), 'keypress', function (i) {
                                 if (Event.getKeyCode(i) == KEYS.RETURN) this._handleButton(this._clickButtonOnEnter);
                                 return true;
                             }.bind(this));
            
            //run onload handlers and clean it
            for (var f = 0; f < this._onload_handlers.length; ++f) try {
                this._onload_handlers[f]();
            } catch (e) {}
            this._onload_handlers = [];

        } else this.showLoading();

        var width = 2 * Dialog._BORDER_WIDTH;
        if (Dialog._useCSSBorders)
            width += 2 * Dialog._HALO_WIDTH;
        if (this._content_width) {
            width += this._content_width;
            if (!this._full_bleed)
                width += 2 * Dialog._PADDING_WIDTH;
        } else if (this._wide_dialog) {
            width += Dialog.SIZE.WIDE;
        } else width += Dialog.SIZE.STANDARD;
        //this._popup.style.width = width + 'px';
        this._popup.style.width = Dialog.SIZE.WIDE + 'px';
    },
    _updateButtons: function () {
        if (!this._showing) return;
        var b = this._getButtonContent();
        var c = null;
        if (!this.getRoot()) this._buildDialog();
        CSS.conditionClass(this.getRoot(), 'omitDialogFooter', !b.length);
        if (b.length) c = $N('div', {
            className: 'dialog_buttons clearfix'
        }, b);
        var d = DOM.scry(this._content, 'div.dialog_buttons')[0] || null;
        if (!d) {
            if (!c) return;
            var a = this.getBody();
            if (a) DOM.insertAfter(a, c);
        } else if (c) {
            DOM.replace(d, c);
        } else DOM.remove(d);
    },
    _getButtonContent: function () {
        var btns = [];
        if ((this._buttons && this._buttons.length > 0) || this._buttons_message) {
            //add buttons message
            if (this._buttons_message)
                btns.push($N('div', {className: 'dialog_buttons_msg'}, this._format(this._buttons_message)));

            if (this._buttons)
                for (var i = 0; i < this._buttons.length; i++) {
                    var button = this._buttons[i];
                    var btn_obj = $N('label', {className: 'uiButton uiButtonLarge uiButtonConfirm'},
                               $N('input', {
                                      type: 'button',
                                      name: button.name || '',
                                      value: button.label
                                  }));
                    if (button.className) {
                        button.className.split(/\s+/).each(
                            function (e) {
                                CSS.addClass(btn_obj, 'e');
                            });
                        if (CSS.hasClass(btn_obj,'inputaux')) {
                            CSS.removeClass(btn_obj, 'inputaux');
                            CSS.removeClass(btn_obj, 'uiButtonConfirm');
                        }
                    }
                    Event.listen(btn_obj.firstChild, 'click', this._handleButton.bind(this, button.name));
                    btns.push(btn_obj);
                }
        }
        return btns;
    },
    _renderDialog: function (b) {
        if (Dialog.dialogStack === null) {
            // KeyEventController.registerKey('ESCAPE', Dialog._handleEscapeKey, Dialog._keyDownFilter);
            onleaveRegister(Dialog._tearDown);
            Arbiter.subscribe('page_transition', Dialog._tearDown);
        }
        if (!this._obj) this._buildDialog();
        if (this._class_name) CSS.addClass(this._obj, this._class_name);
        CSS.conditionClass(this._obj, 'full_bleed', this._full_bleed);
        if (typeof b == 'string') b = HTML(b).setDeferred(this._immediate_rendering !== true);
        DOM.setContent(this._content, b);
        this._showDialog();
        if (this._auto_focus) Form.focusFirst.bind(this, this._content).defer();
        var a = Vector2.getElementDimensions(this._content).y + Vector2.getElementPosition(this._content).y;
        Dialog._bottoms.push(a);
        this._bottom = a;
        Dialog._updateMaxBottom();
        return this;
    },
    _buildDialog: function () {
        this._obj = $N('div', {
            className: 'generic_dialog',
            tabIndex: '0'
        });
        this._obj.setAttribute('role', 'alertdialog');
        this._obj.setAttribute('aria-labelledby', 'title_' + this._uniqueID);
        this._obj.style.display = 'none';
        DOM.getRootElement().appendChild(this._obj);
        if (!this._popup) this._popup = $N('div', {
            className: 'generic_dialog_popup'
        });
        if (!this._bpuo) this._bpuo = $N('div', {
           className : 'bpuo'
        });
        this._popup.style.left = this._popup.style.top = '';
        this._obj.appendChild(this._popup);
        this._obj.appendChild(this._bpuo);
        this._buildDialogContent();
    },
    _showDialog: function () {
        if (this._modal) if (this._overlay) {
            this._overlay.style.display = '';
        } else this._buildOverlay();
        if (this._obj && this._obj.style.display) {
            this._obj.style.visibility = 'hidden';
            this._obj.style.display = '';
            this._resetDialog();
            this._obj.style.visibility = '';
            this._obj.dialog = this;
        } else this._resetDialog();
        clearInterval(this.active_hiding);
        this.active_hiding = setInterval(this._activeResize.bind(this), 500);
        if (!Dialog.dialogStack) Dialog.dialogStack = [];
        var c = Dialog.dialogStack;
        if (c.length) {
            var a = c[c.length - 1];
            if (a != this && (!a._is_stackable || (a._show_loading && a._loading_was_shown))) a._hide();
            for (var b = c.length - 1; b >= 0; b--) if (c[b] == this) {
                c.splice(b, 1);
            } else if (!c[b]._shown_while_stacked) c[b]._hide(true);
        }
        c.push(this);
        return this;
    },
    _activeResize: function () {
        if (this.last_offset_height != this._content.offsetHeight) this.last_offset_height = this._content.offsetHeight;
    },
    _buildDialogContent: function () {
        CSS.addClass(this._obj, 'pop_dialog');
        // if (intl_locale_is_rtl()) CSS.addClass(this._obj, 'pop_dialog_rtl');
        var a;
        if (Dialog._useCSSBorders) {
            a = '<div class="pop_container_generic">' + '<div class="pop_content" id="pop_content"></div>' + '</div>';
        } else a = '<div class="pop_container">' + '<div class="pop_verticalslab"></div>' + '<div class="pop_horizontalslab"></div>' + '<div class="pop_topleft"></div>' + '<div class="pop_topright"></div>' + '<div class="pop_bottomright"></div>' + '<div class="pop_bottomleft"></div>' + '<div class="pop_content pop_content_old" id="pop_content"></div>' + '</div>';
        DOM.setContent(this._popup, HTML(a));
        this._frame = DOM.find(this._popup, 'div.pop_content');
        this._content = this._frame;
    },
    _buildOverlay: function () {
        this._overlay = $N('div', {
            id: 'generic_dialog_overlay'
        });
        if (this._modal_class) CSS.addClass(this._overlay, this._modal_class);
        if (this._semi_modal) {
            var a = function (b) {
                if (b.getTarget() == this._obj || b.getTarget() == this._overlay) this.hide();
            }.bind(this);
            Event.listen(this._obj, 'click', a);
            Event.listen(this._overlay, 'click', a);
        }
        if (ua.ie() < 7) this._overlay.style.height = Vector2.getDocumentDimensions().y + 'px';
        onloadRegister(function () {
            document.body.appendChild(this._overlay);
        }.bind(this));
    },
    _resetDialog: function () {
        if (!this._popup) return;
        this._resetDialogObj();
    },
    _resetDialogObj: function () {
        var c = DOM.find(this._popup, 'div.pop_content');
        var b = Vector2.getScrollPosition().y;
        var f = Vector2.getViewportDimensions().y;
        var d = Vector2.getElementDimensions(c).y;
        var e = b + this._top + 'px';
        if (this._top + d > f) {
            var a = Math.max(f - d, 0);
            e = ((a / 2) + b) + 'px';
        }
		var windowWidth = document.documentElement.clientWidth;
		var windowHeight = document.documentElement.clientHeight;
		var popupHeight = $(".pop_content").height() + 120;
		var popupWidth = $(".generic_dialog_popup").width();
		
		var sw = (screen.width - Dialog.SIZE.WIDE - 20)/2 + 'px';
		this._popup.style.left = (windowWidth/2-popupWidth/2) + 'px';
		
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
		var s_h = scrollTop + document.documentElement.clientHeight/2 - 120;
		
		if ( jQuery.browser.msie && jQuery.browser.version < 7) { // take away IE6
			$(this._popup).css("position","absolute");
			this._popup.style.top =  s_h + 'px';	
		}else{
			this._popup.style.top = (windowHeight/2-popupHeight/2) + 'px';	
		}
		
    },
    _fadeOut: function (b) {
        if (!this._popup) return;
        try {
            animation(this._obj).duration(0).checkpoint().to('opacity', 0).hide().duration(250).ondone(this._hide.bind(this, b)).go();
        } catch (a) {
            this._hide(b);
        }
    },
    _hide: function (modal) {
        if (this._obj)
            this._obj.style.display = 'none';

        //for overlay
        if (this._overlay)
            if (modal) {
                this._overlay.style.display = 'none';
            } else {
                DOM.remove(this._overlay);
                this._overlay = null;
            }

        //clear timeout
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }
        
        //clear hidden_objestc
        if (this._hidden_objects.length) {
            for (var b = 0, c = this._hidden_objects.length; b < c; b++)
                this._hidden_objects[b].style.visibility = '';
            this._hidden_objects = [];
        }
        
        //clear active hiding
        clearInterval(this.active_hiding);
        
        //update for bottoms
        if (this._bottom) {
            var a = Dialog._bottoms;
            a.splice(a.indexOf(this._bottom), 1);
            Dialog._updateMaxBottom();
        }

        if (modal) return;

        this.destroy();
    },
    destroy: function () {
        if (Dialog.dialogStack && Dialog.dialogStack.length) {
            var b = Dialog.dialogStack;
            for (var a = b.length - 1; a >= 0; a--) if (b[a] == this) b.splice(a, 1);
            if (b.length) b[b.length - 1]._showDialog();
        }
        if (this._obj) {
            DOM.remove(this._obj);
            this._obj = null;
        }
        if (this._close_handler) this._close_handler({
            return_data: this._return_data
        });
    },
    _handleButton: function (a) {
        if (typeof a == 'string') a = Dialog._findButton(this._buttons, a);
        if (!a) return;
        var b = Dialog.call_or_eval(a, a.handler);
        if (b === false) return;
        if (a.name == 'cancel') {
            this.cancel();
        } else if (Dialog.call_or_eval(this, this._handler, {
            button: a
        }) !== false) this.hide();
    },
    _submitForm: function (d, e, b) {
        var c = this.getFormData();
        if (b) c[b.name] = b.label;
        if (this._extra_data) copy_properties(c, this._extra_data);
        var a = new AsyncRequest().setURI(e).setData(c).setMethod(d).setReadOnly(d == 'GET');
        this.setAsync(a);
        return false;
    },

    /**
     * set properties from properties model
     */
    _setFromModel: function (model) {
        var properties = {};
        copy_properties(properties, model);
        for (var key in properties) {
            if (key == 'onloadRegister') {
                this.onloadRegister(properties[key]);
                continue;
            }
            var fn = this['set' + key.substr(0, 1).toUpperCase() + key.substr(1)];
            if (!!fn) {
                fn.apply(this, to_array(properties[key]));
            }
        }
    },
    _updateBottom: function () {
        var a = Vector2.getElementDimensions(this._content).y + Vector2.getElementPosition(this._content).y;
        Dialog._bottoms[Dialog._bottoms.length - 1] = a;
        Dialog._updateMaxBottom();
    }
});

/**
 * AsyncRequest
 */
function AsyncRequest(uri) {
    /**
     * 
     */
    var dispatchResponse = bind(
        this, function (asyncResponse) {
            try {
                this.clearStatusIndicator();

                //check initialHandler
                if (this.initialHandler(asyncResponse) !== false) {
                    clearTimeout(this.timer);

                    if (this.handler) try {
                        //call handler
                        var suppress_onload = this.handler(asyncResponse);
                    } catch (exception) {
                        //has some exception, call finallyHandler
                        asyncResponse.is_last && this.finallyHandler(asyncResponse);
                        throw exception;
                    }
                    
                    //call finallyHandler
                    asyncResponse.is_last && this.finallyHandler(asyncResponse);

                    if (suppress_onload !== AsyncRequest.suppressOnloadToken) {
                        var onload = asyncResponse.onload;
                        //call response onloads
                        if (onload) for (var ii = 0; ii < onload.length; ii++) try {
                            (new Function(onload[ii])).apply(this);
                        } catch (exception) {}
                        // if (this.lid && !asyncResponse.isReplay())
                        //     Arbiter.inform(
                        //         'tti_ajax', {
                        //             s: this.lid,
                        //             d: [this._sendTimeStamp || 0, (this._sendTimeStamp && this._responseTime) ? (this._responseTime - this._sendTimeStamp) : 0]
                        //         }, Arbiter.BEHAVIOR_EVENT);
                       
                        //call response on after load
                        var onafterload = asyncResponse.onafterload;
                        if (onafterload) for (var ii = 0; ii < onafterload.length; ii++) try {
                            (new Function(onafterload[ii])).apply(this);
                        } catch (exception) {}
                    }
                    // var invalidate_cache = asyncResponse.invalidate_cache;
                    // if (!this.getOption('suppressCacheInvalidation') && invalidate_cache && invalidate_cache.length) Arbiter.inform(Arbiter.PAGECACHE_INVALIDATE, invalidate_cache);
                }
                // if (asyncResponse.cacheObservation && typeof(TabConsoleCacheobserver) != 'undefined' && TabConsoleCacheobserver.instance) TabConsoleCacheobserver.getInstance().addAsyncObservation(asyncResponse.cacheObservation);
            } catch (exception) {}
        });
    // var replayResponses = bind(this, function () {
    //     if (is_empty(this._asyncResponses)) return;
    //     this.setNewSerial();
    //     for (var ii = 0; ii < this._asyncResponses.length; ++ii) {
    //         var r = this._asyncResponses[ii];
    //         invokeResponseHandler(r, true);
    //     }
    // });
    var dispatchErrorResponse = bind(this, function (asyncResponse,isTransport) {
        try {
            this.clearStatusIndicator();
            var async_error = asyncResponse.getError();
            if (this._sendTimeStamp) {
                var _duration = (+new Date()) - this._sendTimeStamp;
                var xfb_ip = this._xMfSer || '-';
                asyncResponse.logError('async_error', _duration + ':' + xfb_ip);
            } else asyncResponse.logError('async_error');

            // if ((!this.isRelevant()) || async_error === 1010) return;

            if (async_error == 1357008 || async_error == 1357007 || async_error == 1442002 || async_error == 1357001) {
                var is_confirmation = false;
                if (async_error == 1357008 || async_error == 1357007) is_confirmation = true;
                var payload = asyncResponse.getPayload();
                this._displayServerDialog(payload.__dialog, is_confirmation);
            } else if (this.initialHandler(asyncResponse) !== false) {
                clearTimeout(this.timer);
                try {
                    if (isTransport) {
                        this.transportErrorHandler(asyncResponse);
                    } else this.errorHandler(asyncResponse);
                } catch (exception) {
                    this.finallyHandler(asyncResponse);
                    throw exception;
                }
                this.finallyHandler(asyncResponse);
            }
        } catch (exception) {}
    });

    /**
     * @return: {asyncResponse: response}
     */
    var _interpretTransportResponse = bind(this, function () {
        var _sendError = function (request, error_code, str) {
            if (!window.send_error_signal) return;
            send_error_signal('async_xport_resp', error_code + ':' + (this._xMfSer || '-') + ':' + request.getURI() + ':' + str.length + ':' + str.substr(0, 1600));
        };
        var shield = "for (;;);";
        var shieldlen = shield.length;
        var text = this.transport.responseText;
        if (text.length <= shieldlen) {
            _sendError(this, '1008_empty', text);
            return {
                transportError: 'Response too short on async to ' + this.getURI()
            };
        }
        var offset = 0;
        while (text.charAt(offset) == " " || text.charAt(offset) == "\
") offset++;
        // offset && text.substring(offset, offset + shieldlen) == shield;
        var safeResponse = text.substring(offset + shieldlen);
        try {
            var response = eval('(' + safeResponse + ')');
        } catch (exception) {
            _sendError(this, '1008_excep', text);
            return {
                transportError: 'eval() failed on async to ' + this.getURI()
            };
        }
        return interpretResponse(response);
    });

    /**
     * create AsyncResponse from response
     * @return: {asyncResponse: response}
     */
    var interpretResponse = bind(this, function (response) {
        if (response.redirect) return {
            redirect: response.redirect
        };
        var async_response = new AsyncResponse(this);
        if (typeof(response.payload) == 'undefined' ||
            typeof(response.error) == 'undefined' ||
            typeof(response.errorDescription) == 'undefined' ||
            typeof(response.errorSummary) == 'undefined' ||
            typeof(response.errorIsWarning) == 'undefined') {
            async_response.payload = response;
        } else copy_properties(async_response, response);
        return {
            asyncResponse: async_response
        };
    });

    /**
     * invoke the response handler
     * @param interp: AsyncResponse
     */
    var invokeResponseHandler = bind(
        this, function (interp) {
            if (typeof(interp.redirect) != 'undefined') {
                //use the same request.handler to request another uri
                (function () {
                     this.setURI(interp.redirect).send();
                 }).bind(this).defer();
                return;
            }
            if (!(this.handler || this.errorHandler || this.transportErrorHandler)) {
                //for has no handler
                return;
            }

            if (typeof(interp.asyncResponse) != 'undefined') {
                var r = interp.asyncResponse;
                //eval inline script
                if (r.inlinejs) eval_global(r.inlinejs);
                // if (r.lid) {
                //     this._responseTime = (+new Date());
                //     if (window.CavalryLogger) this.cavalry = CavalryLogger.getInstance(r.lid);
                //     this.lid = r.lid;
                // }
                if (r.getError() && !r.getErrorIsWarning()) {
                    //has error
                    var fn = dispatchErrorResponse;
                } else {
                    //response success
                    var fn = dispatchResponse;
                }
                Bootloader.setResourceMap(r.resource_map);
                if (r.bootloadable) {
                    Bootloader.enableBootload(r.bootloadable);
                }
                //defer call dispatchResponse
                fn = fn.shield(null, r);
                fn = fn.defer.bind(fn);
                
                var is_transitional = false;
                // if (this.preBootloadHandler)
                //     is_transitional = this.preBootloadHandler(r);
                
                //load resources
                r.css = r.css || [];
                r.js = r.js || [];
                Bootloader.loadResources(r.css.concat(r.js), fn, is_transitional);
            } else if (typeof(interp.transportError) != 'undefined') {
                if (this._xMfSer) {
                    invokeErrorHandler(1008);
                } else invokeErrorHandler(1012);
            } else invokeErrorHandler(1007);
        });
    var invokeErrorHandler = bind(
        this, function (explicitError) {
            try {
                if (!window.loaded) return;
            } catch (ex) {
                return;
            }
            var r = new AsyncResponse(this);

            //error number
            var err;
            try {
                err = explicitError || this.transport.status || 1004;
            } catch (ex) {
                err = 1005;
            }
            if (this._requestAborted) err = 1011;
            try {
                if (this.responseText == '') err = 1002;
            } catch (ignore) {}

            if (this.transportErrorHandler) {
                var desc, summary;
                var silent = true;
                if (false === navigator.onLine) {
                    summary = "No Network Connection";
                    desc = "Your browser appears to be offline. Please check your internet connection and try again.";
                    err = 1006;
                    silent = false;
                } else if (err >= 300 && err <= 399) {
                    summary = "Redirection";
                    desc = "Your access was redirected or blocked by a third party at this time, please contact your ISP or reload. ";
                    redir_url = this.transport.getResponseHeader("Location");
                    if (redir_url) goURI(redir_url, true);
                    silent = true;
                } else {
                    summary = "Oops";
                    desc = "Something went wrong. We're working on getting this fixed as soon as we can. You may be able to try again.";
                }!this.getOption('suppressErrorAlerts');
                copy_properties(r, {
                                    error: err,
                                    errorSummary: summary,
                                    errorDescription: desc,
                                    silentError: silent
                                });
                dispatchErrorResponse(r, true);
            }
        });
    var handleResponse = function (response) {
        var asyncResponse = this.interpretResponse(response);
        this.invokeResponseHandler(asyncResponse);
    };
    var onStateChange = function () {
        try {
            if (this.transport.readyState == 4) {
                //get server id
                try {
                    if (typeof(this.transport.getResponseHeader) != 'undefined' && this.transport.getResponseHeader('X-MF-Server'))
                        this._xMfSer = this.transport.getResponseHeader('X-MF-Server');
                } catch (ex) {}

                if (this.transport.status >= 200 && this.transport.status < 300) {
                    //invoke response handler
                    invokeResponseHandler(_interpretTransportResponse());
                } else if (ua.safari() && (typeof(this.transport.status) == 'undefined')) {
                    invokeErrorHandler(1002);
                } else if (window.send_error_signal && window.Env && window.Env.retry_ajax_on_network_error && this.transport.status in {
                    0: 1,
                    12029: 1,
                    12030: 1,
                    12031: 1,
                    12152: 1
                } && this.remainingRetries > 0) {
                    //retry
                    --this.remainingRetries;
                    delete this.transport;
                    this.send(true);
                    return;
                } else invokeErrorHandler();
                if (this.getOption('asynchronous') !== false) delete this.transport;
            }
        } catch (exception) {
            try {
                if (!window.loaded) return;
            } catch (ex) {
                return;
            }
            delete this.transport;
            if (this.remainingRetries > 0) {
                --this.remainingRetries;
                this.send(true);
            } else {
                // !this.getOption('suppressErrorAlerts');
                if (window.send_error_signal) send_error_signal('async_xport_resp', '1007:' + (this._xMfSer || '-') + ':' + this.getURI() + ':' + exception.message);
                invokeErrorHandler(1007);
            }
        }
    };
    var onJSONPResponse = function (data, more_chunked_response) {
        var is_first = (this.is_first === undefined);
        this.is_first = is_first;
        //clear iframe
        if (this.transportIframe && !more_chunked_response)(function (x) {
            document.body.removeChild(x);
        }).bind(null, this.transportIframe).defer();

        if (ua.ie() >= 9 && window.JSON)
            data = window.JSON.parse(window.JSON.stringify(data));

        var r = this.interpretResponse(data);
        r.asyncResponse.is_first = is_first;
        r.asyncResponse.is_last = !more_chunked_response;
        this.invokeResponseHandler(r);
        return more_chunked_response;
    };
    copy_properties(this, {
        onstatechange: onStateChange,
        onjsonpresponse: onJSONPResponse,
        // replayResponses: replayResponses,
        invokeResponseHandler: invokeResponseHandler,
        interpretResponse: interpretResponse,
        handleResponse: handleResponse,
        transport: null,
        method: 'POST',
        uri: '',
        timeout: null,
        timer: null,
        initialHandler: bagofholding,
        handler: null,
        errorHandler: null,
        transportErrorHandler: null,
        timeoutHandler: null,
        finallyHandler: bagofholding,
        serverDialogCancelHandler: bagofholding,
        relativeTo: null,
        statusElement: null,
        statusClass: '',
        data: {},
        context: {},
        readOnly: false,
        writeRequiredParams: ['MF_CSRF_TOKEN'],
        remainingRetries: 0,
        option: {
            asynchronous: true,
            suppressCacheInvalidation: false,
            suppressErrorHandlerWarning: false,
            suppressErrorAlerts: false,
            retries: 0,
            jsonp: false,
            // bundle: false,
            useIframeTransport: false,
            //test facebook endpoint
            tfbEndpoint: true
        },
        _isPrefetch: false
    });
    this.errorHandler = AsyncResponse.defaultErrorHandler;
    this.transportErrorHandler = bind(this, 'errorHandler');
    if (uri != undefined) this.setURI(uri);
    return this;
}
copy_properties(AsyncRequest, {
    pingURI: function (uri, data, b) {
        data = data || {};
        return new AsyncRequest().setURI(uri).setData(data).setOption('asynchronous', !b).setOption('suppressErrorHandlerWarning', true).setErrorHandler(bagofholding).setTransportErrorHandler(bagofholding).send();
    },
    receiveJSONPResponse: function (b, a, c) {
        if (this._JSONPReceivers[b])
            if (!this._JSONPReceivers[b](a, c))
                delete this._JSONPReceivers[b];
    },
    // _hasBundledRequest: function () {
    //     return AsyncRequest._allBundledRequests.length > 0;
    // },
    // stashBundledRequest: function () {
    //     var a = AsyncRequest._allBundledRequests;
    //     AsyncRequest._allBundledRequests = [];
    //     return a;
    // },
    // setBundledRequestProperties: function (b) {
    //     var c = null;
    //     if (b.stashedRequests) AsyncRequest._allBundledRequests = AsyncRequest._allBundledRequests.concat(b.stashedRequests);
    //     if (!AsyncRequest._hasBundledRequest()) {
    //         var a = b.callback;
    //         a && a();
    //     } else {
    //         copy_properties(AsyncRequest._bundledRequestProperties, b);
    //         if (b.start_immediately) c = AsyncRequest._sendBundledRequests();
    //     }
    //     return c;
    // },
    // _bundleRequest: function (request) {
    //     if (request.getOption('jsonp') || request.getOption('useIframeTransport')) {
    //         request.setOption('bundle', false);
    //         return false;
    //     } else if (!request.uri.isMoofaURI()) {
    //         request.setOption('bundle', false);
    //         return false;
    //     } else if (!request.getOption('asynchronous')) {
    //         request.setOption('bundle', false);
    //         return false;
    //     }
    //     var path = request.uri.getPath();
    //     if (!AsyncRequest._bundleTimer)
    //         AsyncRequest._bundleTimer = setTimeout(
    //             function () {
    //                 AsyncRequest._sendBundledRequests();
    //             }, 0);
    //     AsyncRequest._allBundledRequests.push([path, request]);
    //     return true;
    // },
    // _sendBundledRequests: function () {

    //     clearTimeout(AsyncRequest._bundleTimer);
    //     AsyncRequest._bundleTimer = null;
    //     var a = AsyncRequest._allBundledRequests;
    //     AsyncRequest._allBundledRequests = [];
    //     var e = {};
    //     copy_properties(e, AsyncRequest._bundledRequestProperties);
    //     AsyncRequest._bundledRequestProperties = {};
    //     if (is_empty(e) && a.length == 1) {
    //         var g = a[0][1];
    //         g.setOption('bundle', false).send();
    //         return g;
    //     }
    //     var d = function () {
    //         e.callback && e.callback();
    //     };
    //     if (a.length === 0) {
    //         d();
    //         return null;
    //     }
    //     var b = [];
    //     for (var c = 0; c < a.length; c++) b.push([a[c][0], URI.implodeQuery(a[c][1].data)]);
    //     var f = {
    //         data: b
    //     };
    //     if (e.extra_data) copy_properties(f, e.extra_data);
    //     var g = new AsyncRequest();
    //     g.setURI('/ajax/proxy.php').setData(f).setMethod('POST').setInitialHandler(e.onInitialResponse || bagof(true)).setAllowCrossPageTransition(true).setHandler(function (l) {
    //         var k = l.getPayload();
    //         var n = k.responses;
    //         if (n.length != a.length) {
    //             return;
    //         } else
    //         for (var i = 0; i < a.length; i++) {
    //             var j = a[i][0];
    //             var m = a[i][1];
    //             m.id = this.id;
    //             if (n[i][0] != j) {
    //                 m.invokeResponseHandler({
    //                     transportError: 'Wrong response order in bundled request to ' + j
    //                 });
    //                 continue;
    //             }
    //             var h = m.interpretResponse(n[i][1]);
    //             m.invokeResponseHandler(h);
    //         }
    //     }).setTransportErrorHandler(function (m) {
    //         var k = [];
    //         var i = {
    //             transportError: m.errorDescription
    //         };
    //         for (var h = 0; h < a.length; h++) {
    //             var j = a[h][0];
    //             var l = a[h][1];
    //             k.push(j);
    //             l.id = this.id;
    //             l.invokeResponseHandler(i);
    //         }
    //     }).setFinallyHandler(function (h) {
    //         d();
    //     }).send();
    //     return g;
    // },
    bootstrap: function (uri, node, d) {
        var method = 'GET';
        var f = true;
        var a = {};
        if (d || (node && node.rel == 'async-post')) {
            method = 'POST';
            f = false;
            if (uri) {
                uri = URI(uri);
                a = uri.getQueryData();
                uri.setQueryData({});
            }
        }
        var g = Parent.byClass(node, 'stat_elem') || node;
        if (g && CSS.hasClass(g, 'async_saving')) return false;
        new AsyncRequest(uri).setReadOnly(f).setMethod(method).setData(a).setNectarModuleDataSafe(node).setStatusElement(g).setRelativeTo(node).send();
        return false;
    },
    post: function (uri, data) {
        new AsyncRequest(uri).setReadOnly(false).setMethod('POST').setData(data).send();
        return false;
    },
    // clearCache: function () {
    //     AsyncRequest._reqsCache = {};
    // },
    getLastId: function () {
        return AsyncRequest._last_id;
    },
    _JSONPReceivers: {},
    // _allBundledRequests: [],
    // _bundledRequestProperties: {},
    // _bundleTimer: null,
    suppressOnloadToken: {},
    _last_id: 2,
    _id_threshold: 2
    // _reqsCache: {}
});
copy_properties(AsyncRequest.prototype, {
    /**
     * set request method (GET/POST)
     */
    setMethod: function (a) {
        this.method = a.toString().toUpperCase();
        return this;
    },
    /**
     * set request method (GET/POST)
     */
    getMethod: function () {
        return this.method;
    },
    /**
     * set query data params
     */
    setData: function (a) {
        this.data = a;
        return this;
    },
    /**
     * set query data params
     */
    getData: function () {
        return this.data;
    },
    /**
     * set context data
     */
    setContextData: function (key, value, cond) {
        cond = cond === undefined ? true : cond;
        if (cond) this.context['_log_' + key] = value;
        return this;
    },
    /**
     * set request URI
     */
    setURI: function (uri) {
        var b = URI(uri);
        //not Moofa uri must use IframeTransport
        if (!this.getOption('useIframeTransport') && !b.isMoofaURI())
            return this;
        //not the same Origin must use jsonp or useIframeTransport
        if (!this.getOption('jsonp') && !this.getOption('useIframeTransport') && !b.isSameOrigin())
            return this;
        if (!uri || b.toString() === '') {
            //empty uri
            if (window.send_error_signal && window.get_error_stack) {
                send_error_signal('async_error', '1013:-:0:-:' + window.location.href);
                send_error_signal('async_xport_stack', '1013:' + window.location.href + '::' + get_error_stack());
            }
            return this;
        }
        this.uri = b;
        return this;
    },
    getURI: function () {
        return this.uri.toString();
    },

    /**
     * handler before dispatch response
     */
    setInitialHandler: function (handler) {
        this.initialHandler = handler;
        return this;
    },
    setHandler: function (fn) {
        if (!(typeof fn != 'function')) this.handler = fn;
        return this;
    },
    getHandler: function () {
        return this.handler;
    },
    setErrorHandler: function (fn) {
        if (!(typeof fn != 'function')) this.errorHandler = fn;
        return this;
    },
    setTransportErrorHandler: function (fn) {
        this.transportErrorHandler = fn;
        return this;
    },
    getErrorHandler: function () {
        return this.errorHandler;
    },
    getTransportErrorHandler: function () {
        return this.transportErrorHandler;
    },
    setTimeoutHandler: function (time_s, fn) {
        if (!(typeof fn != 'function')) {
            this.timeout = time_s;
            this.timeoutHandler = fn;
        }
        return this;
    },
    resetTimeout: function (time_s) {
        if (!(this.timeoutHandler === null)) if (time_s === null) {
            this.timeout = null;
            clearTimeout(this.timer);
            this.timer = null;
        } else {
            this.timeout = time_s;
            clearTimeout(this.timer);
            this.timer = this._handleTimeout.bind(this).defer(this.timeout);
        }
        return this;
    },
    /**
     * abandon request
     * call timeoutHandler
     */
    _handleTimeout: function () {
        this.abandon();
        this.timeoutHandler(this);
    },
    setNewSerial: function () {
        this.id = ++AsyncRequest._last_id;
        return this;
    },
    setFinallyHandler: function (fn) {
        this.finallyHandler = fn;
        return this;
    },
    setServerDialogCancelHandler: function (fn) {
        this.serverDialogCancelHandler = fn;
        return this;
    },
    setPreBootloadHandler: function (fn) {
        //handler before bootload res
        // return true means early Resources need to drop/unload
        this.preBootloadHandler = fn;
        return this;
    },
    setReadOnly: function (read_only) {
        if (!(typeof(read_only) != 'boolean'))
            this.readOnly = read_only;
        return this;
    },
    setFBMLForm: function () {
        this.writeRequiredParams = ["mf_sig"];
        return this;
    },
    getReadOnly: function () {
        return this.readOnly;
    },
    setRelativeTo: function (a) {
        this.relativeTo = a;
        return this;
    },
    getRelativeTo: function () {
        return this.relativeTo;
    },
    setStatusClass: function (class_name) {
        this.statusClass = class_name;
        return this;
    },
    setStatusElement: function (id) {
        this.statusElement = id;
        return this;
    },
    getStatusElement: function () {
        return ge(this.statusElement);
    },
    clearStatusIndicator: function () {
        var obj = this.getStatusElement();
        if (obj) {
            obj.removeClass('async_saving');
            obj.removeClass(this.statusClass);
        }
    },
    addStatusIndicator: function () {
        var obj = this.getStatusElement();
        if (obj) {
            CSS.addClass(obj, 'async_saving');
            CSS.addClass(obj, this.statusClass);
        }
    },
    /**
     * check every wirte Required param has value
     */
    specifiesWriteRequiredParams: function () {
        return this.writeRequiredParams.every(
            function (param) {
                this.data[param] = this.data[param] || Env[param] || (ge(param) || {}).value;
                if (this.data[param] !== undefined) return true;
                return false;
        }, this);
    },
    setOption: function (key, value) {
        if (typeof(this.option[key]) != 'undefined')
            this.option[key] = value;
        return this;
    },
    getOption: function (key) {
        // typeof(this.option[key]) == 'undefined';
        return this.option[key];
    },
    /**
     * abort the request
     */
    abort: function () {
        if (this.transport) {
            var old_transportErrorHandler = this.getTransportErrorHandler();
            this.setOption('suppressErrorAlerts', true);
            this.setTransportErrorHandler(bagofholding);
            this._requestAborted = 1;
            this.transport.abort();
            this.setTransportErrorHandler(old_transportErrorHandler);
        }
    },
    /**
     * abandon request
     */
    abandon: function () {
        clearTimeout(this.timer);
        this.setOption('suppressErrorAlerts', true).setHandler(bagofholding).setErrorHandler(bagofholding).setTransportErrorHandler(bagofholding);
        if (this.transport) {
            this._requestAborted = 1;
            this.transport.abort();
        }
    },
    // setNectarActionData: function (a) {
    //     if (this.data.nctr === undefined) this.data.nctr = {};
    //     this.data.nctr._ia = 1;
    //     if (a) {
    //         if (this.data.nctr._as === undefined) this.data.nctr._as = {};
    //         copy_properties(this.data.nctr._as, a);
    //     }
    //     return this;
    // },
    // setNectarData: function (a) {
    //     if (a) {
    //         if (this.data.nctr === undefined) this.data.nctr = {};
    //         copy_properties(this.data.nctr, a);
    //     }
    //     return this;
    // },
    // setNectarModuleDataSafe: function (a) {
    //     if (this.setNectarModuleData) this.setNectarModuleData(a);
    //     return this;
    // },
    // setNectarImpressionIdSafe: function () {
    //     if (this.setNectarImpressionId) this.setNectarImpressionId();
    //     return this;
    // },
    /**
     * send request
     */
    send: function (once) {
        if (!this.uri) return false;

        once = once || false;
        // !this.errorHandler && !this.getOption('suppressErrorHandlerWarning');
        
        //jsonp can only GET
        if (this.getOption('jsonp') && this.method != 'GET') this.setMethod('GET');
        //iframe can only GET
        if (this.getOption('useIframeTransport') && this.method != 'GET') this.setMethod('GET');

        // this.timeoutHandler !== null && (this.getOption('jsonp') || this.getOption('useIframeTransport'));

        if (!this.getReadOnly()) {
            //check for readonly
            if (!this.specifiesWriteRequiredParams()) return false;
            if (this.method != 'POST') return false;
        }

        // if (this.method == 'POST' && this.getOption('tfbEndpoint')) {
        //     this.data.fb_dtsg = Env.fb_dtsg;
        //     this.data.lsd = getCookie('lsd');
        // }

        // this._replayable = (!this.getReadOnly() && this._replayable !== false) || this._replayable;
        // if (this._replayable) Arbiter.inform(AsyncRequest.REPLAYABLE_AJAX, this);

        if (!is_empty(this.context) && this.getOption('tfbEndpoint')) {
            //add test log info
            copy_properties(this.data, this.context);
            this.data.ajax_log = 1;
        }
        //for test reason
        if (!this.getReadOnly() && this.getOption('tfbEndpoint') && this.method == 'POST' && this.data.post_form_id_source === undefined) this.data.post_form_id_source = 'AsyncRequest';

        //not use bundle request now
        // if (this.getOption('bundle') && AsyncRequest._bundleRequest(this)) return true;

        this.setNewSerial();
        
        // //test again
        // if (this.getOption('tfbEndpoint')) this.uri.addQueryData({
        //     __a: 1
        // });

        // var b = env_get('haste_combo');
        // if (b) setCookie('force_hcfb', 1, 1000);

        // this.finallyHandler = async_callback(this.finallyHandler, 'final');


        //url and query params
        var url, params;
        if (this.method == 'GET') {
            url = this.uri.addQueryData(this.data).toString();
            params = '';
        } else {
            url = this.uri.toString();
            params = URI.implodeQuery(this.data);
        }

        //send jsonp or iframe request
        if (this.getOption('jsonp') || this.getOption('useIframeTransport')) {
            url = this.uri.addQueryData({
                // __a: this.id
            }).toString();
            AsyncRequest._JSONPReceivers[this.id] = bind(this, 'onjsonpresponse');
            if (this.getOption('jsonp')) {
                //add jsonp script
                (function () {
                    document.body.appendChild($N('script', {
                        src: url,
                        type: "text/javascript"
                    }));
                }).bind(this).defer();
            } else {
                //for iframe transport
                var style = {
                    position: 'absolute',
                    top: '-1000px',
                    left: '-1000px',
                    width: '80px',
                    height: '80px'
                };
                this.transportIframe = $N('iframe', {
                    src: url,
                    style: style
                });
                document.body.appendChild(this.transportIframe);
            }
            //request sent
            return true;
        }

        //transport has not set yet
        if (this.transport)
            return false;

        //for normal request
        var req = null;
        try {
            req = new XMLHttpRequest();
        } catch (ex) {}
        if (!req) try {
            req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (ex) {}
        if (!req) try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (ex) {}
        //req must have made successfull
        if (!req) return false;

        req.onreadystatechange = bind(this, 'onstatechange');
        //set if can retry
        if (!once) this.remainingRetries = this.getOption('retries');

        if (window.send_error_signal || window.ArbiterMonitor)
            this._sendTimeStamp = this._sendTimeStamp || (+new Date());

        this.transport = req;
        try {
            this.transport.open(this.method, url, this.getOption('asynchronous'));
        } catch (ex) {
            //request open fail
            return false;
        }
        this.transport.setRequestHeader('X-REQUESTED-WITH', 'XMLHttpRequest');
        // var g = env_get('svn_rev');
        // if (g) this.transport.setRequestHeader('X-SVN-Rev', String(g));

        if (this.method == 'POST')
            this.transport.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        this.addStatusIndicator();
        this.transport.send(params);

        //reset timeout
        if (this.timeout !== null)
            this.resetTimeout(this.timeout);
        return true;
    },
    _displayServerDialog: function (c, b) {
        var a = new Dialog(c);
        if (b) a.setHandler(this._displayConfirmationHandler.bind(this, a));
        a.setCancelHandler(function () {
            this.serverDialogCancelHandler.apply(this, arguments);
            this.finallyHandler.apply(this, arguments);
        }.bind(this)).setCloseHandler(this.finallyHandler.bind(this)).show();
    },
    _displayConfirmationHandler: function (a) {
        this.data.confirmed = 1;
        copy_properties(this.data, a.getFormData());
        this.send();
    }
});

/**
 * object AsyncResponse
 */
function AsyncResponse(async_request, response_payload) {
    copy_properties(this, {
        error: 0,
        errorSummary: null,
        errorDescription: null,
        onload: null,
        payload: response_payload || null,
        request: async_request || null,
        silentError: false,
        is_last: true
    });
    return this;
}
copy_properties(AsyncResponse, {
    /**
     * default response error handler
     */
    defaultErrorHandler: function (response) {
        try {
            if (!response.silentError) {
                AsyncResponse.verboseErrorHandler(response);
            } else if (typeof(window.Env) == 'undefined' || typeof(window.Env.silent_oops_errors) == 'undefined') {
                AsyncResponse.verboseErrorHandler(response);
            } else response.logErrorByGroup('silent', 10);
        } catch (a) {
            alert(b);
        }
    },

    /**
     * popup error dialog for response
     */
    verboseErrorHandler: function (response) {
        try {
            var summary = response.getErrorSummary();
            var desc = response.getErrorDescription();
            response.logErrorByGroup('popup', 10);
            if (response.silentError && desc == '')
                desc = "Something went wrong. We're working on getting this fixed as soon as we can. You may be able to try again.";
            ErrorDialog.show(summary, desc);
        } catch (exception) {
            alert(exception);
        }
    }
});
copy_properties(AsyncResponse.prototype, {
    getRequest: function () {
        return this.request;
    },
    getPayload: function () {
        return this.payload;
    },
    getError: function () {
        return this.error;
    },
    getErrorSummary: function () {
        return this.errorSummary;
    },
    setErrorSummary: function (str) {
        str = (str === undefined ? null : str);
        this.errorSummary = str;
        return this;
    },
    getErrorDescription: function () {
        return this.errorDescription;
    },
    getErrorIsWarning: function () {
        return this.errorIsWarning;
    },
    /**
     * log error
     */
    logError: function (error_name, info) {
        if (window.send_error_signal) {
            info = (info === undefined ? '' : (':' + info));
            var uri = this.request.getURI();
            var str = this.error + ':' + (env_get('vip') || '-') + info + ':' + uri;
            send_error_signal(error_name, str);
        }
    },

    /**
     * log error group
     */                    
    logErrorByGroup: function (group, times) {
        if (Math.floor(Math.random() * times) == 0) {
            if (this.error == 1357010 || this.error < 15000) {
                this.logError('async_error_oops_' + group);
            } else this.logError('async_error_logic_' + group);
        }
    }
});
