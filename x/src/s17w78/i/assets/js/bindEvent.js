function NodeObject(obj) {
    obj = ge(obj);

    var nodes = to_array(obj.childNodes);
    for (var i=0; i < nodes.length; i++) {
        if (nodes[i].nodeName.toLowerCase() == 'script') {
            delete nodes[i];
        }
    }
    copy_properties(this, {
        _nodes: nodes,
        _rendered: obj,
        _subobjs: [],
        _parentobj: null
    });
    NodeObject.bindNodes(this._nodes, this);
}

copy_properties(
    NodeObject, {
        push_sub_object: function(sub_obj, obj) {
            obj._subobjs.push(sub_obj);
            sub_obj._parentobj = obj;
        },
        bindNodes: function (nodes, obj) {
            for(var i = 0; i < nodes.length; i++) {
                //no parse script
                if (nodes[i].nodeName.toLowerCase() == 'script' ||
                   nodes[i].nodeType != 1) {
                    continue;
                }

                //for bind point
                var point = nodes[i].getAttribute('bindPoint');
                if (point) {
                    if (can_as_array(obj[point])) {
                        obj[point].push(nodes[i]);
                    } else {
                        obj[point] = nodes[i];
                    }
                    obj.update_node_events(point, nodes[i]);
                }

                //add listen event if exist
                var listen = nodes[i].getAttribute('listen');
                if (listen) listen.replace(/(\w+) *: *(\w+)/g, function (node, match_str, event_str, callback) {
                    if (typeof(this[callback]) == "function") {
                        Event.listen(node, event_str, bind(this, this[callback]));
                    } else {
                        Event.listen(node, event_str, bind(this, NodeObject._eventWrapper, callback));
                    }
                }.bind(obj, nodes[i]));

                //for sub nodes or sub object
                var conn = nodes[i].getAttribute('role');
                if ( conn && typeof(window[conn]) == 'function') {
                    //has conn
                    var sub_obj = new window[conn](nodes[i]);
                    NodeObject.push_sub_object(sub_obj, obj);
                    if (typeof(obj["addSubObject"]) == 'function') {
                        obj.addSubObject(sub_obj);
                    }
                } else {
                    //no conn
                    NodeObject.bindNodes(to_array(nodes[i].childNodes), obj);
                }
            }

            // var all_nodes = [];
            // for (var i = 0; i < nodes.length; i++) {
            //     if (nodes[i].nodeType == 1) {
            //         all_nodes.push(nodes[i]);
            //         var sub_nodes = nodes[i].getElementsByTagName('*');
            //         all_nodes = all_nodes.concat(to_array(sub_nodes));
            //     }
            // }
            // for (var j = 0; j < all_nodes.length; j++) {
            //     var listen = all_nodes[j].getAttribute('listen');
            //     if (listen) listen.replace(/(\w+) *: *(\w+)/g, function (node, match_str, event_str, callback) {
            //         if (typeof(this[callback]) == "function") {
            //             Event.listen(node, event_str, bind(this, this[callback]));
            //         } else {
            //             Event.listen(node, event_str, bind(this, NodeObject._eventWrapper, callback));
            //         }
            //     }.bind(obj, all_nodes[j]));
            // }
        },
        _eventWrapper: function (callback, event) {
            if (typeof(this[callback]) == "function") {
                return this[callback](event);
            } else
                return true;
        }
});
copy_properties(NodeObject.prototype, {
    getNodes: function () {
        return this._nodes;
    },
    getRendered: function () {
        return this._rendered;
    },
    update_node_events: function(point, node) {
    }
});
/**
 * container now use for show in dialog
 */
var traversal = function(node, container) {
    if (node.nodeType != 1) {
        return;
    }
    var object_name = node.getAttribute('role');
    if (object_name && typeof(window[object_name]) == 'function') {
        new window[object_name](node, container);
    } else {
        var sub_nodes = to_array(node.childNodes);
        for (var i = 0; i < sub_nodes.length; i++) {
            traversal(sub_nodes[i], container);
        }
    }
};

Arbiter.registerCallback(
    function () {
        traversal(document.getElementsByTagName('body')[0]);
}.bind(window),[OnloadEvent.ONLOAD_DOMCONTENT]);

function DialogFormController(node, container) {
    copy_properties(
        this, {
            submitButton: null,
            redirect_elmt: null,
            errorSummary: null,
            inputs: [],
            _disable: false,
            _dialog: container
            // _postUri: null,
            // _redirectUri:null
            //threads key by topicID
        });
    this.parent.construct(this, node);
}
DialogFormController.extend('NodeObject');
copy_properties(
    DialogFormController.prototype, {
        disable: function() {
            this._disable = true;
            for (var i = 0; i < this.inputs.length; i++) {
                this.inputs[i].disabled = true;
            }
            CSS.addClass(this.submitButton.parentNode, 'uiButtonDisabled');
        },
        enable: function() {
            this._disable = false;
            for (var i = 0; i < this.inputs.length; i++) {
                this.inputs[i].disabled = false;
            }
            CSS.removeClass(this.submitButton.parentNode, 'uiButtonDisabled');
        },
        getPostUri: function() {
            if (this._postUri == undefined) {
                if(this.submitButton != null) {
                    var submit_form = Parent.byTag(this.submitButton, "form");
                    if (submit_form) {
                        this._postUri = submit_form.action;
                    }
                }
            }
            return this._postUri;
        },
        getRedirectUri: function () {
            if (this._redirectUri == undefined && this.redirect_elmt) {
                this._redirectUri = this.redirect_elmt.value;
            }
            return this._redirectUri;
        },
        post: function () {
            if (this._disable) {
                return false;
            }
            var uri = this.getPostUri();
            if (typeof(uri) == "string") {
                var data = Form.serialize(this._rendered);
                this.disable();
                var request = new AsyncRequest().setURI(uri).setHandler(this.handleResponse.bind(this)).setErrorHandler(this.handleError.bind(this)).setFinallyHandler(this.enable.bind(this)).setData(data).send();
            }
            return false;
        },
        defaultRedirect: function(response) {
            var uri = this.getRedirectUri();
            if (typeof(uri) == "string") {
                window.location.href = uri;
            }
        },
        updateDialog: function(response) {
            var dialog;
            if (this._dialog) {
                dialog = this._dialog;
            } else {
                dialog = new Dialog;
            }
            var model = response.getPayload();
            var update = function() {
                if (typeof model == "string") {
                    this.setBody(model);
                } else {
                    this._setFromModel(model);
                }
                this._update(true);
            }.bind(dialog);
            update();
        },
        handleResponse: function(response) {
            this.defaultRedirect(response);
        },
        handleError: function(response) {
            this.setError(response.getErrorSummary(), response.getErrorDescription());
        },
        setError: function(summary, desc) {
            var error_body;
            if (typeof desc == "string") {
                error_body = desc;
            } else if (typeof desc == "object") {
                error_body = '<ul>';
                for (var key in desc) {
                    error_body += '<li>'+desc[key]+'</li>';
                }
                error_body += '</ul>';
            } else {
                error_body = '<ul>';
                desc = to_array(desc);
                for (var i=0; i < desc.length; i++) {
                    error_body += '<li>'+desc[i]+'</li>';
                }
                error_body = '</ul>';
            }
            var error_html = '<div class="errorSummary"><p>'+summary+'</p>'+error_body+'</div>';
            this.errorSummary.innerHTML = error_html;
        }
    }
);
