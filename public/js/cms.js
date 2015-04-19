var CMS = {
    plug: {},
    kit: {},
    tmpl: {},
    api: {},
    regular: {} // 整个平台的所有正则

};

/* kit */
CMS.kit = {
    formatTime: function(source, pattern) { // date format
        // Jun.com.format(new Date(),"yyyy-MM-dd hh:mm:ss");
        //Jun.com.format(new Date(),"yyyy年MM月dd日 hh时:mm分:ss秒");
        source = new Date(source);
        var pad = this.pad,
            date = {
                yy: String(source.getFullYear()).slice(-2),
                yyyy: source.getFullYear(),
                M: source.getMonth() + 1,
                MM: pad(source.getMonth() + 1, 2, '0'),
                d: source.getDate(),
                dd: pad(source.getDate(), 2, '0'),
                h: source.getHours(),
                hh: pad(source.getHours(), 2, '0'),
                m: source.getMinutes(),
                mm: pad(source.getMinutes(), 2, '0'),
                s: source.getSeconds(),
                ss: pad(source.getSeconds(), 2, '0')
            };
        return (pattern || "yyyy-MM-dd hh:mm:ss").replace(/yyyy|yy|MM|M|dd|d|hh|h|mm|m|ss|s/g, function(v) {
            return date[v];
        });

    },
    tmpl: function(str, data) {
        var fn = !/\W/.test(str) ?
            cache[str] = cache[str] ||
            tmpl(document.getElementById(str).innerHTML) :

        new Function("obj",
            "var p=[],print=function(){p.push.apply(p,arguments);};" +
            "with(obj){p.push('" +

            str
            .replace(/[\r\t\n]/g, " ")
            .split("<%").join("\t")
            .replace(/((^|%>)[^\t]*)'/g, "$1\r")
            .replace(/\t=(.*?)%>/g, "',$1,'")
            .split("\t").join("');")
            .split("%>").join("p.push('")
            .split("\r").join("\\'") + "');}return p.join('');");

        return data ? fn(data) : fn;
    },
    formatFormData: function(form) {
        var datas = form.serializeArray();
        var result = {};
        for (var i = 0; i < datas.length; i++) {
            if (!result[datas[i].name]) {
                result[datas[i].name] = datas[i].value;
            } else if (result[datas[i].name] && $.isArray(result[datas[i].name])) {
                result[datas[i].name].push(datas[i].value);
            } else {
                var val = result[datas[i].name];
                result[datas[i].name] = [];
                result[datas[i].name].push(val, datas[i].value);
            }
        };
        return result;
    },
    isJSONAllValue: function(jsonData, ignoreKey) {
        var json = jsonData;
        var isAllValue = true;
        ignoreKey = typeof ignoreKey == "undefined" ? [] : ignoreKey;
        for (var i in json) {
            if($.inArray(i, ignoreKey) > -1) continue;
            if ($.isArray(json[i])) {
                if (!json[i].length) {
                    return false;
                }
            } else if (json[i] == "") {
                return false;
            }
        }
        return isAllValue;
    },
    getParameter: function(url) {
        var array = [];
        var object = {};
        var param = url || window.location.search;
        if (param.indexOf('?') > -1) {
            param = param.substring(1);
        }

        array = param.split('&');
        for (var i = 0, len = array.length; i < len; i++) {
            var p = array[i].split('=');
            object[p[0]] = p[1];
        }

        return object;
    },
    pageUnLoad: function() {
        window.onbeforeunload = function() {
            return '确定退出？';
        }
    },
    checkStrLen: function(a) {
        for (var c = 0, b = 0; b < a.length; b++) c += a.charCodeAt(b) < 0 || a.charCodeAt(b) > 255 ? 2 : 1;
        return c
    }
};

/*文件上传插件*/
CMS.kit.fileUp = function(data) {
    this.fileID = data.fileID ? data.fileID : null;
    this.url = data.url ? data.url : '';
    this.data = data.data ? data.data : null;
    this.dom = data.dom ? data.dom : null;
}

$.extend(CMS.kit.fileUp.prototype, {
    init: function() {
        var _this = this;

        _this.fileUp();
    },
    fileUp: function() {
        var _this = this;
        $.ajaxFileUpload({
            url: _this.url,
            secureuri: false,
            fileElementId: _this.fileID,
            dataType: 'json',
            data: _this.data,
            success: function(data) {
                _this.update(data);
            },
            error: function(data, status, e) {
                _this.error(data, status, e);
            }
        });
    },
    update: function() {

    },
    error: function() {

    }
});


/* ajax form */
CMS.Form = function() {}
CMS.Form.prototype = {
    post: function(url, data) {
        var _this = this;
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            error: function() {
                _this.error();
            },
            success: function(data) {
                _this.response(data);
            }
        })
    },
    get: function(url, data) {
        var _this = this;
        $.ajax({
            url: url,
            type: 'get',
            data: data,
            dataType: 'json',
            error: function(a, b) {
                _this.error();
            },
            success: function(data) {
                _this.response(data);
            }
        });
    },
    error: function() {
        window.console && console.log('出错了');
    },
    response: function(data) {}
};



/* plugin */


/**
 * 批号名称检测
 */
CMS.plug.checkPageName = function() {
    if ($('#addSaveBtn').length > 0) {
        var input = $('input[name=package_name]');
        $('#addSaveBtn').click(function() {
            var value = $.trim(input.val());
            if (CMS.kit.checkStrLen(value) > 100) {
                alert('批号名称不能超过100个字符');
            }
            return false;
        })
    }
};

/**
 * 加载谷歌地图
 * @method CMS.plug.loadGoogleMap
 */
CMS.plug.loadGoogleMap = function() {

    var loadSource = function() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyB4chIbMkOrx2MY67lXSq5jl4NspyeJMHE&sensor=false&libraries=places&";
        document.body.appendChild(script);
    }
    window.onload = loadSource;
}


/**
 * 话费充值接口
 * @method CMS.plug.PhonefareSelect
 * @param {String} domID 节点ID的值
 * @param {Number} addressCode 地址的code值
 */
CMS.plug.PhonefareSelect = function(domID, name, clas) {

    domID.html('...');
    var self = this;
    var html = '';
    var form = new CMS.api.PhonefareModel();
    form.response = function(data) {
        if (data.result) {
            var datas = data.data;
            html += '<select name="' + name + '" class="' + clas + '">';
            for (var i = 0; i < datas.length; i++) {
                html += '<option value="' + datas[i]._id.$id + '">' + datas[i].city + '</option>';
            }
            html += '</select>';

            domID.html(html);
        }
    }
    form.getCodes();
};



/**
 * 地址联动
 * @method CMS.plug.AddressLinkage
 * @param {String} domID 节点ID的值
 * @param {Number} addressCode 地址的code值
 */
CMS.plug.AddressLinkage = function(domID, addressCode, codeName) {

    this.domID = domID;
    this.codeName = codeName || 'area_code';
    this.loading();


    var self = this;
    var form = new CMS.api.AddressLinkageForm();
    form.response = function(data) {
        if (data.result) {
            self.create(data.data);
        }
    };
    form.getFullAddress(addressCode);
};

CMS.plug.AddressLinkage.prototype = {

    regEvent: function() {

        var self = this;

        this.ui.wrap.delegate('select', 'change', function() {
            var $this = $(this);
            var value = parseInt($this.find('option:selected').val());
            var index = $this.index();
            self.updateName($this);
            self.getFollows(value, index);
        });
    },

    create: function(data) {
        console.log('data', data);
        var html = CMS.kit.tmpl(CMS.tmpl.addressLinkage, {
            data: data,
            codeName: this.codeName
        });
        html = $(html);
        this.ui = {
            wrap: html
        };
        $('#' + this.domID).empty().append(this.ui.wrap);
        this.regEvent();
    },

    loading: function() {
        $('#' + this.domID).text('...');
    },

    updateName: function($this) {
        var value = $this.find('option:selected').val();
        if (value == "?") {
            $this.removeAttr('name');
        } else {
            $this.attr('name', this.codeName);
        }
    },
    getFollows: function(addressCode, index) {
        if (!addressCode) {
            this.ui.wrap.find('select:gt(' + index + ')').remove();
            return false;
        }

        var self = this;
        var form = new CMS.api.AddressLinkageForm();
        form.response = function(data) {
            if (data.result && data.data.length) {
                self.update(data.data, index);
            }
        };
        form.getFollowAddress(addressCode);
    },

    update: function(data, index) {

        this.ui.wrap.find('select:gt(' + index + ')').remove();
        data.unshift({
            id: "?",
            name: "请选择"
        });
        var html = CMS.kit.tmpl(CMS.tmpl.addressLinkageItem, {
            data: data
        });
        this.ui.wrap.append(html);
    },

    onGetAddress: function() {
        var result = [];

        this.ui.wrap.find('select').each(function(index) {
            var value = $(this).find('option:selected').val();
            if (value != "?") {
                result.push(value);
            }
        });

        return result;
    }
};

/**

  点选地址坐标

*/

/**
 * 谷歌地区坐标选择
 * @method CMS.plug.GoogleCood
 */
;(function(window, $) {
	var map, marker, googleMap;

	googleMap = {
		initMap: function(x, y, eleId) {
			var zoom, myLatlng, mapOptions;
			
			zoom = 15;

			x = x || 22.539889;
			y = y || 114.052452;

			myLatlng = new google.maps.LatLng(x, y);
			mapOptions = {
				zoom: zoom,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			eleId = eleId || '#map';
			
			map = new google.maps.Map($(eleId)[0], mapOptions);
			//map.setZoom(1);
			this.placeMarker(myLatlng, zoom, true, '');
			this.bindEvents();
		},
		placeMarker: function(latLng, zoom, setCenter, title) {
			marker && marker.setVisible(false);
			marker = new google.maps.Marker({
				position: latLng,
				map: map,
				title: title || ""
			});

			if (setCenter) {
				map.setCenter(latLng);
			}

			if (zoom != undefined) {
				map.setZoom(zoom);
			}
			googleMap._onLatLngChange(latLng.lat(), latLng.lng());
		},
		search: function(text, zoom) {
			var latLng = map.getCenter(), dfd = $.Deferred();
			zoom = zoom || 15;
			var _this = this;
			var service = new google.maps.places.PlacesService(map);
			service.textSearch({
				location: latLng,
				radius: '5000',
				query: text
			}, function(result) {
				if (result && result.length) {
					var location = result[0].geometry.location;
					_this.placeMarker(location, zoom, true, result[0].name);
					dfd.resolve(text, {lat: location.lat(), lng: location.lng()});
				} else {
					dfd.reject(text);
				}
			});
			return dfd;
		},
		_onLatLngChange: function(lat, lng) {
			this.lat = lat;
			this.lng = lng;
			this.onLatLngChange && this.onLatLngChange(lat, lng);
		},

		bindEvents: function() {
			var _this = this;
			google.maps.event.addListener(map, 'click', function(event) {
				_this.placeMarker(event.latLng, undefined, false);
			});
		}
	};

	window.googleMap = googleMap;
})(window, jQuery);

;(function(window, $) {
	var map, marker, point, baiduMap;

	baiduMap = {
		/*
		 * 初始化map对象
		 */
		initMap: function (lat, lng, eleId) {
			var lat = lat || 22.539889,
				lng = lng || 114.052452,
				zoom = 15,
				eleId = eleId || '#map';

			baiduMap.mapDom = $(eleId)[0];
			map = new BMap.Map(baiduMap.mapDom);
			point = new BMap.Point(lng, lat); // 创建点坐标
			baiduMap.map = map;

			map.centerAndZoom(point, zoom); // 初始化地图,设置中心点坐标和地图级别。
			map.enableScrollWheelZoom(); //启用滚轮放大缩小

			marker = new BMap.Marker(); // 创建标注
			marker.setPosition(point);

			map.addOverlay(marker);
			baiduMap._onLatLngChange(point.lat, point.lng);
			baiduMap.bindEvents();
		},

		/*
		 * 搜索接口
		 * @param value [string]
		 * @param zoom [string] 缩放比例
		 */
		search: function(value) {
			map.clearOverlays(); //清除地图上所有覆盖物

			var  dfd = $.Deferred(),
				local = new BMap.LocalSearch(map, { //智能搜索
					onSearchComplete: function(result) {
						var p = result.getPoi(0); //获取第一个智能搜索的结果

						if(p.point){
							point = p.point;
							map.setCenter(point);
							marker.setPosition(point);
							map.addOverlay(marker);
							baiduMap._onLatLngChange(point.lat, point.lng);
							dfd.resolve(value, {lng: point.lng, lat: point.lat});
						} else {
							dfd.reject(value);// 搜索不到
						}
					}
				});
			local.search(value);
			return dfd;
		},

		_onLatLngChange: function(lat, lng){
			this.lat = lat;
			this.lng = lng;
			this.onLatLngChange && this.onLatLngChange(lat, lng);
		},

		bindEvents: function() {
			var _this = this;
			map.addEventListener('click', function(e){
				point = e.point;
				marker.setPosition(point);
				map.addOverlay(marker);
				baiduMap._onLatLngChange(e.point.lat, e.point.lng);
			});

			marker.enableDragging();
			marker.addEventListener("dragend", function(e){
				 baiduMap._onLatLngChange(e.point.lat, e.point.lng);
			});
		}
	}

	window.baiduMap = baiduMap;
})(window, jQuery);

CMS.plug.latLngSelector = {
	loadMap: (function(){
		var dfd = $.Deferred();
		return function(lat, lng, useBaidu) {
			var googleSrc = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB4chIbMkOrx2MY67lXSq5jl4NspyeJMHE&sensor=false&libraries=places&callback=googleCallback';
			var baiduSrc = "http://api.map.baidu.com/api?v=2.0&ak=QWFA7ZKCQxjOIOoEBI178nlc&callback=baiduCallback";
			var script = document.createElement('script');
			var self = this;
			script.type = 'text/javascript';
			
			self.show();  // 显示弹出框
			self.regUIEvent();
			
			if(dfd.isResolved()){
				self.init(lat, lng);
				return dfd;
			}

			// 根据超时来决定使用哪个地图
			var img = new Image();

			window.baiduCallback = function(){
				self.M = baiduMap;
				dfd.resolve(self);
				self.init(self.M.lat);
			}

			window.googleCallback = function(){
				self.M = googleMap;
				dfd.resolve(self);
				self.init(self.M.lat);
			}


			img.onerror = img.onload = function(){
				script.src = googleSrc;
			};
			setTimeout(function(){
				img.onerror = img.onload = function(){};
				script.src = baiduSrc;
			}, 2000);
			
			if(useBaidu){  // 使用百度地图
				script.src = baiduSrc;
			} else {  // 根据超时选地图
				img.src = googleSrc;
			}

			if(!dfd.isResolved() && !dfd.sending){
				document.body.appendChild(script);
				dfd.sending = true;
			}
			return dfd;
		}
	})(),

	init: function(){
		var lat = this.M.lat, lng = this.M.lng;
		this.M.initMap(lat, lng, this.ui.map[0]);
		this.regMapEvent();
	},
	
	regMapEvent: function() {
		var self = this;
		self.M.onLatLngChange = function(lat, lng){
			self.setResult('当前选定', lat, lng);
		}
	},

	regUIEvent: function() {

		var self = this;
		this.ui.wrap.find('.close-btn').click(function() {
			self.ui.wall.remove();
			return false;
		});

		this.ui.searchBtn.click(function() {
			var text = $.trim(self.ui.searchInput.val());
			if (text != "") {
				self.M.search(text);
			}
			return false;
		});

		this.ui.searchInput.keyup(function(e) {
			if (e.keyCode == 13) {
				self.ui.searchBtn.click();
			}
		});

		this.ui.getBtn.click(function() {
			var lat = self.M.lat, lng = self.M.lng;
			self.ui.wall.remove();
			if (lat != null && lng != null) {
				self.onSelectLatLng(lat, lng);
			} else {
				self.onSelectLatLng('', '');
			}
			return false;
		});
	},

	show: function() {
		var html = $(CMS.tmpl.googleCoordinate);
		var wrap = $(html);
		this.ui = {
			wall: wrap,
			wrap: wrap.find('.plug-googleCood-map'),
			map: wrap.find('.map'),
			searchBtn: wrap.find('.search-btn'),
			searchInput: wrap.find('.search-input'),
			coodTags: wrap.find('.cood-tags'),
			getBtn: wrap.find('.get-btn')
		};
		$('body').append(this.ui.wall);
	},

	showSearching: function(place) {
		this.ui.coodTags.text('正在搜索“' + place + '”...').removeClass('hide');
	},

	stopSearching: function(place) {
		var txt = place || '相关信息';
		this.ui.coodTags.text('找不到“' + place + "”");
	},

	setResult: function(text, lat, lnt) {
		this.ui.coodTags.text('" ' + text + '" 的经纬坐标: ' + lat + ' ' + lnt).removeClass('hide');
	}
};

/**
 * 选中checkbox,input禁止
 * @method CMS.plug.InputSwitchAble
 * @param {jQDom} checkbox checkbox节点
 * @param {jQDom} inputs input(s)节点
 */
CMS.plug.InputSwitchAble = function(checkbox, inputs) {

    this.ui = {
        checkbox: checkbox,
        inputs: inputs
    }

    console.log('', this.ui.inputs);
    this.regEvent();
};

CMS.plug.InputSwitchAble.prototype = {
    regEvent: function() {
        var self = this;

        this.ui.checkbox.change(function() {
            var isCheck = self.ui.checkbox.attr('checked');
            isCheck = typeof isCheck == "undefined" ? false : true;
            console.log('isCheck', isCheck);
            if (isCheck) {
                self.disable();
            } else {
                self.enable();
            }
        });
    },

    enable: function() {
        this.ui.inputs.each(function(index) {
            $(this).removeAttr('disabled');
        });
    },

    disable: function() {
        this.ui.inputs.each(function(index) {
            console.log('disable', $(this));
            $(this).val('').attr('disabled', 'disabled');
        });
    }
};



/**
 * 简易的弹出层[只提供简单的content设置]
 * @method CMS.plug.PopupLayout
 * @param {String} content 内容的html字符串
 * @param {String} id 可以给弹出层设置一个id
 * @param {String} oktext 弹出层确定按钮的文字
 * @param {function} onRegEvent content内容的事件绑定
 * @param {function} onConfirm 点击保存触发的事件
 */
CMS.plug.PopupLayout = function(content, id, oktext) {
    this.id = id || '';
    this.content = content;
    this.oktext = oktext || '添加';
    var html = CMS.kit.tmpl(CMS.tmpl.popuplayout, {
        id: this.id,
        oktext: this.oktext
    });
    var wrap = $(html);

    this.ui = {
        wrap: wrap,
        content: wrap.find('.modal-body'),
        close: wrap.find('.close-btn'),
        ok: wrap.find('.ok-btn')
    };

    this.ui.content.html(content);
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.PopupLayout.prototype = {
    init: function() {
        this.regEvent();
    },
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.close();
            return false;
        });
        this.ui.ok.click(function() {
            self.onConfirm();
            return false;
        });
        this.onRegEvent();
    },
    onDestroy: function() {
        this.ui.wrap.remove();
    },
    close: function() {
        this.ui.wrap.remove();
    },
    onRegEvent: function() {},
    onConfirm: function() {}

};


/**
 * 项目搜索弹出层
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.ProjectSearch = function() {

    var wrap = $(CMS.tmpl.projectSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-projectSearch-btn'),
        form: wrap.find('.plug-projectSearch-form'),
        results: wrap.find('.plug-projectSearch-results'),
        pageBox: wrap.find('.plug-projectSearch-pagebox'),
        thanksBtn: wrap.find('.plug-projectSearch-thanks')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});
    this.regEvent();
    this.currPage = 1;

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.ProjectSearch.prototype = {
    tmpl: '<tr>\
                <td><%=num%></td>\
                <td><%=package_number%></td>\
                <td><%=package_name%></td>\
                <td><%=quantity%></td>\
                <td><%=available_quantity%></td>\
                <td><%=used_quantity%></td>\
                <td><button class="btn btn-small btnAdd" data-num="<%=num-1%>">添加</button></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {

            var params = CMS.kit.formatFormData(self.ui.form);
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.search(params, 1);
            //}
            return false;
        });

        this.ui.results.delegate('.btnAdd', 'click', function(event) {
            var data = self.datas[parseInt($(this).data('num'))];
            self.onSelect(data);
        });

        this.ui.thanksBtn.click(function() {
            self.onSelect({
                package_name: '谢谢你'
            })
            return false;
        });
    },

    search: function(params, page) {
        var self = this;
        var form = new CMS.api.PojectSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);
            }
        }

        form.search(params.type, params.package_name, params.package_number, page);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }

        this.ui.results.html(html.join('\n'));
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};


/**
 * 新魔商搜索(刮刮卡)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.ScratchSearch = function() {

    var wrap = $(CMS.tmpl.ScratchSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-scratchSearch-btn'),
        form: wrap.find('.plug-scratchSearch-form'),
        results: wrap.find('.plug-scratchSearch-results'),
        pageBox: wrap.find('.plug-scratchSearch-pagebox'),
        thanksBtn: wrap.find('.plug-scratchSearch-thanks')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.ScratchSearch.prototype = {
    tmpl: '<tr>\
                <td><%=num%></td>\
                <td><%=shop_id%></td>\
                <td><%=shop_name%></td>\
                <td><button class="btn btn-small btnAdd" data-num="<%=num-1%>">添加</button></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {

            var params = CMS.kit.formatFormData(self.ui.form);

            $.extend(params, {
                'page': 1
            });
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.search(params);
            //}
            return false;
        });

        this.ui.results.delegate('.btnAdd', 'click', function(event) {
            var data = self.datas[parseInt($(this).data('num'))];
            self.onSelect(data);
        });

        this.ui.thanksBtn.click(function() {
            self.onSelect({
                package_name: '谢谢你'
            })
            return false;
        });
    },

    search: function(params, page) {
        var self = this;

        $.extend(params, {
            'page': page
        });

        var form = new CMS.api.PojectSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);

                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setPage();
            }
        }

        form.moSearch(params);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }

        this.ui.results.html(html.join('\n'));
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};


/**
 *  
 池刮刮卡项目ID
 * @method CMS.plug.PrizePool
 */
CMS.plug.PrizePoolSearch = function() {
    var wrap = $(CMS.tmpl.PrizePoolSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-prizePoolSearch-btn'),
        form: wrap.find('.plug-prizePoolSearch-form'),
        results: wrap.find('.plug-prizePoolSearch-results'),
        pageBox: wrap.find('.plug-prizePoolSearch-pagebox'),
        thanksBtn: wrap.find('.plug-prizePoolSearch-thanks')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});
    this.regEvent();
    this.currPage = 1;

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.PrizePoolSearch.prototype = {
    tmpl: '<tr>\
                <td><%=num%></td>\
                <td><%=date%></td>\
                <td><%=address%></td>\
                <td><%=number_code%></td>\
                <td><button class="btn btn-small btnAdd" data-num="<%=num-1%>">添加</button></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {

            var params = CMS.kit.formatFormData(self.ui.form);

            $.extend(params, {
                'page': 1
            });
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.search(params);
            //}
            return false;
        });

        this.ui.results.delegate('.btnAdd', 'click', function(event) {
            var data = self.datas[parseInt($(this).data('num'))];
            self.onSelect(data);
        });

        this.ui.thanksBtn.click(function() {
            self.onSelect({
                package_name: '谢谢你'
            })
            return false;
        });
    },

    search: function(params, page) {
        var self = this;

        $.extend(params, {
            'page': page
        });

        var form = new CMS.api.PojectSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);

                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setPage();
            }
        }

        form.getPrizePool(params);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }

        this.ui.results.html(html.join('\n'));
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};



/**
 * 优惠券搜索弹出层
 * @method CMS.plug.GiftSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.GiftSearch = function() {
    var wrap = $(CMS.tmpl.giftSearch);


    this.currPage = 1;
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-giftSearch-btn'),
        form: wrap.find('.plug-giftSearch-form'),
        results: wrap.find('.plug-giftSearch-results'),
        table: wrap.find('.plug-giftSearch-table'),
        pageBox: wrap.find('.plug-giftSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.GiftSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].g_name %></td>\
                        <td><%=datas[i].g_num %></td>\
                        <td>\
                            <button class="btn btn-small plug-giftSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, self.currPage);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-giftSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchGift(params.g_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.search(params, num);
            }
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 文章搜索弹出层
 * @method CMS.plug.ArticleSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.ArticleSearch = function() {
    var wrap = $(CMS.tmpl.articleSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-articleSearch-btn'),
        form: wrap.find('.plug-articleSearch-form'),
        results: wrap.find('.plug-articleSearch-results'),
        table: wrap.find('.plug-articleSearch-table')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.ArticleSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].item_name %></td>\
                        <td>\
                            <button class="btn btn-small plug-articleSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-articleSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchArticle(params.item_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 广告搜索弹出层
 * @method CMS.plug.AdSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.AdSearch = function() {
    var wrap = $(CMS.tmpl.adSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-adSearch-btn'),
        form: wrap.find('.plug-adSearch-form'),
        results: wrap.find('.plug-adSearch-results'),
        table: wrap.find('.plug-adSearch-table'),
        pageBox: wrap.find('.plug-adSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.AdSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].item_name %></td>\
                        <td>\
                            <button class="btn btn-small plug-adSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-adSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchAd(params.item_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 视频搜索弹出层
 * @method CMS.plug.VideoSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.VideoSearch = function() {
    var wrap = $(CMS.tmpl.videoSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-videoSearch-btn'),
        form: wrap.find('.plug-videoSearch-form'),
        results: wrap.find('.plug-videoSearch-results'),
        table: wrap.find('.plug-videoSearch-table'),
        pageBox: wrap.find('.plug-videoSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.VideoSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].item_name %></td>\
                        <td>\
                            <button class="btn btn-small plug-videoSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-videoSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchVideo(params.item_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 应用搜索弹出层
 * @method CMS.plug.AppSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.AppSearch = function() {
    var wrap = $(CMS.tmpl.appSearch);

    this.currPage = 1;
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-appSearch-btn'),
        form: wrap.find('.plug-appSearch-form'),
        results: wrap.find('.plug-appSearch-results'),
        table: wrap.find('.plug-appSearch-table'),
        pageBox: wrap.find('.plug-appSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.AppSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].item_name %></td>\
                        <td>\
                            <button class="btn btn-small plug-appSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-appSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchApp(params.item_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 印花图片搜索弹出层
 * @method CMS.plug.StampPicSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.StampPicSearch = function(stampData) {
    var wrap = $(CMS.kit.tmpl(CMS.tmpl.stampPicSearch, {
        data: stampData
    }));
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        results: wrap.find('.plug-stampPicSearch-results'),
        table: wrap.find('.plug-stampPicSearch-table'),
        pageBox: wrap.find('.plug-stampPicSearch-pagebox')
    };

    this.stampData = stampData;
    this.currPage = 1;

    this.search(1);
    this.regEvent();

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};
CMS.plug.StampPicSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><img style="max-width: 50px" src="<%=datas[i].pic %>" class="img-rounded"></td>\
                        <td><%=datas[i].publish_num %></td>\
                        <td>\
                            <button class="btn btn-small plug-stampPicSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.results.delegate('.plug-stampPicSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchStampPic(this.stampData.seed_sid, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                console.log('onclick', num);
                //var params = CMS.kit.formatFormData( self.ui.form );
                self.currPage = num;
                self.search(num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};


/**
 * 印花搜索弹出层
 * @method CMS.plug.StampSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.StampSearch = function() {
    var wrap = $(CMS.tmpl.stampSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-stampSearch-btn'),
        form: wrap.find('.plug-stampSearch-form'),
        results: wrap.find('.plug-stampSearch-results'),
        table: wrap.find('.plug-stampSearch-table'),
        pageBox: wrap.find('.plug-stampSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.StampSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].gift_name %></td>\
                        <td>\
                            <button class="btn btn-small plug-stampSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-stampSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.SearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.searchStamp(params.item_name, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};




/**
 * 优惠券搜索弹出层
 * @method CMS.plug.CouponSearch
 * @params {function} onSelect 点击“选择”之后触发，会返回选中的data, 可重写
 * @params {function} onDestroy 销毁该弹出层
 */
CMS.plug.CouponSearch = function() {
    var wrap = $(CMS.tmpl.couponSearch);
    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-couponSearch-btn'),
        form: wrap.find('.plug-couponSearch-form'),
        results: wrap.find('.plug-couponSearch-results'),
        table: wrap.find('.plug-couponSearch-table'),
        pageBox: wrap.find('.plug-couponSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.currPage = 1;
    this.regEvent();

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.CouponSearch.prototype = {
    resultTemp: '<% var num = 1; for(var i=0;i<datas.length;i++){ %>\
                    <tr>\
                        <td><%=num %></td>\
                        <td><%=datas[i].coupon_name %></td>\
                        <td><%=datas[i].available_count %></td>\
                        <td>\
                            <button class="btn btn-small plug-couponSearch-selectBtn" data-cid="<%=i %>">添加</button>\
                        </td>\
                    </tr>\
                <% num++; } %>',

    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);
            if (CMS.kit.isJSONAllValue(params)) {
                self.search(params, 1);
            }
            return false;
        });
        this.ui.form.submit(function(e) {
            e.preventDefault();
            return false;
        });
        this.ui.results.delegate('.plug-couponSearch-selectBtn', 'click', function() {
            var cid = parseInt($(this).attr('data-cid'), 10);
            self.onSelect(self.datas[cid]);
        });
    },
    search: function(params, page) {
        var self = this;
        var form = new CMS.api.CouponSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setResult(self.datas);
                self.setPage();
            }
        }
        form.search(params.shop_name, params.type, page);
    },
    setResult: function(data) {
        var html = CMS.kit.tmpl(this.resultTemp, {
            datas: data
        });
        this.ui.results.html(html)
        this.ui.table.removeClass('hidden');
    },
    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },
    onSelect: function(data) {},
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};



/**
 * 分页类
 * @method CMS.plug.Pagination
 * @params {Number} currPage 当前页数
 * @params {Number} totalPage 总页数
 * @params {JQDom} parentDom 父级Dom节点
 */
CMS.plug.Pagination = function(currPage, totalPage, parentDom) {

    this.currPage = currPage;
    this.totalPage = totalPage;
    this.showNums = 4;

    var OutTemplate = this.createList();

    OutTemplate = '<ul class="pagination">' + OutTemplate + '</ul>';

    var wrap = $(OutTemplate);
    this.ui = {
        wrap: wrap,
        parentDom: parentDom
    }
    parentDom.empty();
    parentDom.append(this.ui.wrap);
    this.regEvent();
};

CMS.plug.Pagination.prototype = {

    regEvent: function() {
        var self = this;
        this.ui.parentDom.find('a[data-num]').bind('click', function() {
            var num = parseInt($(this).attr('data-num'), 10);
            self.onPage(num);
        });
    },

    onPage: function(num) {},

    btnTemplate: function(side) {

        var template = '';
        var dataNum;

        switch (side) {

            case 'left':
                console.log('this.currPage', this.currPage);
                if (this.currPage !== 1) {
                    dataNum = this.currPage - 1;
                    template = '<li><a data-num=' + dataNum + ' href="javascript:;">«</li>';
                } else {
                    template = '<li class="disabled"><a href="javascript:;">«</a></li>';
                };
                break;

            case 'right':
                if (this.currPage !== this.totalPage) {
                    dataNum = this.currPage + 1;
                    template = '<li><a data-num=' + dataNum + ' href="javascript:;">»</li>';
                } else {
                    template = '<li class="disabled"><a href="javascript:;">»</a></li>';
                };
                break;
        }

        return template;
    },

    listTemplate: function(num) {

        var template = "";

        template = '<li><a data-num="' + num + '" href="javascript:;" class="cms-pagination-list">' + num + '</a></li>';
        if (this.currPage === num) {
            template = '<li class="active"><a class="cms-pagination-currPage">' + num + '</a></li>';
        }
        return template;
    },

    dotTemplate: function() {

        var template = "";

        template = '<li><span class="cms-pagination-dot">...</span>';

        return template;
    },
    createList: function() {
        var OutStr = '';

        OutStr += this.btnTemplate('left');
        if (this.totalPage < 9) {
            var i = 1;
            for (; i <= this.totalPage; i++) {
                OutStr += this.listTemplate(i);
            }
        } else {
            OutStr += this.leftSideList();
            OutStr += this.listTemplate(this.currPage, this.currPage);
            OutStr += this.rightSideList();
        }
        OutStr += this.btnTemplate('right');

        return OutStr;

    },
    leftSideList: function() {
        var OutStr = "";

        if (this.currPage - this.showNums > 2) {
            OutStr += this.listTemplate(1, this.currPage);
            OutStr += this.dotTemplate();

            var i = this.currPage - this.showNums;
            for (; i < this.currPage; i++) {
                OutStr += this.listTemplate(i, this.currPage);
            }
        } else if (this.currPage - this.showNums <= 2) {


            var currPage = this.currPage;
            var i = 1;
            for (; i < currPage; i++) {
                OutStr += this.listTemplate(i, this.currPage);
            }
        }

        return OutStr;
    },

    rightSideList: function() {

        var OutStr = "";
        if (this.currPage + this.showNums < this.totalPage - 1) {
            var i = this.currPage + 1;
            for (; i < this.currPage + this.showNums; i++) {
                OutStr += this.listTemplate(i);
            };
            OutStr += this.dotTemplate();
            OutStr += this.listTemplate(this.totalPage);
        } else if (this.currPage + this.showNums >= this.totalPage - 1) {
            var i = this.currPage + 1;
            for (; i < this.totalPage; i++) {
                OutStr += this.listTemplate(i);
            }
        };
        return OutStr;
    }
};



/**
 * 添加页面的子项列表
 * @method CMS.plug.SubItemList
 */
CMS.plug.SubItemList = function(tbody, pageBox, tmpl, type, package_id, status) {
    this.tmpl = tmpl;
    this.type = type;
    this.package_id = package_id;
	this.status = status;

    this.currPage = 1;

    this.ui = {
        tbody: tbody,
        pageBox: pageBox
    };

    this.init();
};

CMS.plug.SubItemList.prototype = {
    init: function() {

        this.page(this.currPage);
    },

    page: function(page) {
        var self = this;
        var form = new CMS.api.SubItemListForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;



                // if(self.type != 0 && self.type != 1000){
                //  self.type = data.type;
                // }



                self.totalPage = data.page_count;
                self.currPage = data.page;

                self.setResults(data.data);
                if (self.totalPage > 0) {
                    self.setPageBox();
                }

            }
        };

        console.log(this.type, this.package_id)
        form.getSubItemListDatas(this.type, this.package_id, page, this.status);
    },

    setResults: function(data) {
        function fData(data){
            switch(data.length){
                case 1:
                    return data[0];
                case 2:
                    return [data[0], data[1]].join('<br />');
                default:
                    return [data[0], data[1], '...'].join('<br />');
            }
        }
        
        if (data.length) {
            if(data[0].start_number){
                $(data).each(function(i, item){
                    if(this.start_number){
                        this.show_start_number = fData(item.start_number);
                    }
                    if(this.end_number){
                        this.show_end_number = fData(item.end_number);
                    }
                });
            }
            var html = CMS.kit.tmpl(this.tmpl, {
                datas: data
            });

        } else {
            var html = '<tr><td class="muted text-center" colspan="30">没有信息</td></tr>';
        }
        console.log('html', html);
        this.ui.tbody.html(html);
    },

    setPageBox: function() {
        var self = this;
        this.pagination = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
        this.pagination.onPage = function(num) {
            self.page(parseInt(num, 10));
        };
    },

    onGetSelectData: function(num) {
        return {
            data: this.datas[num],
            type: this.type
        };
    },

    onPage: function(page) {
        this.currPage = page || this.currPage;
        this.page(page);
    }
};



/**
 * 添加奖品池子项列表
 * @method CMS.plug.PrizePoolItemList
 */
CMS.plug.PrizePoolItemList = function(tbody, pageBox, tmpl,package_id, status) {
    this.tmpl = tmpl;
    this.package_id = package_id;
    this.status = status;

    this.currPage = 1;

    this.ui = {
        tbody: tbody,
        pageBox: pageBox
    };

    this.init();
};

CMS.plug.PrizePoolItemList.prototype = {
    init: function() {
        this.page(this.currPage);
    },

    page: function(page) {
        var self = this;
        var form = new CMS.api.SubItemListForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;

                self.totalPage = data.page_count;
                self.currPage = data.page;

                self.setResults(data.data);
                if (self.totalPage > 0) {
                    self.setPageBox();
                }

            }
        };
        form.addPrizePoolSubItem(this.package_id, page, this.status);
    },

    setResults: function(data) {
        function fData(data){
            switch(data.length){
                case 1:
                    return data[0];
                case 2:
                    return [data[0], data[1]].join('<br />');
                default:
                    return [data[0], data[1], '...'].join('<br />');
            }
        }
        
        if (data.length) {
            if(data[0].start_number){
                $(data).each(function(i, item){
                    if(this.start_number){
                        this.show_start_number = fData(item.start_number);
                    }
                    if(this.end_number){
                        this.show_end_number = fData(item.end_number);
                    }
                });
            }
            var html = CMS.kit.tmpl(this.tmpl, {
                datas: data
            });

        } else {
            var html = '<tr><td class="muted text-center" colspan="30">没有信息</td></tr>';
        }
        console.log('html', html);
        this.ui.tbody.html(html);
    },

    setPageBox: function() {
        var self = this;
        this.pagination = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
        this.pagination.onPage = function(num) {
            self.page(parseInt(num, 10));
        };
    },

    onGetSelectData: function(num) {
        return {
            data: this.datas[num],
            type: this.type
        };
    },

    onPage: function(page) {
        this.currPage = page || this.currPage;
        this.page(page);
    }
};



/**
 * 添加页面的子项列表
 * @method CMS.plug.MenuRetraction
 */
CMS.plug.MenuRetraction = function(selectID) {
    var nav = $('#' + selectID).closest('.nav');
    nav.removeClass('hidden');
}
CMS.plug.MenuRetraction.prototype = {
    init: function() {

    },
    regEvent: function() {

    }
}


/**
 * 魔商搜索(优惠卷添加)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.moShopSearch = function() {

    var wrap = $(CMS.tmpl.MoShopSearch);

    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-scratchSearch-btn'),
        form: wrap.find('.plug-scratchSearch-form'),
        results: wrap.find('.plug-scratchSearch-results'),
        pageBox: wrap.find('.plug-scratchSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.regEvent();
    this.currPage = 1;

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.moShopSearch.prototype = {
    tmpl: '<tr>\
                <td><%=num%></td>\
                <td><%=shop_id%></td>\
                <td><%=shop_name%></td>\
                <td><button class="btn btn-small btnAdd" data-num="<%=num-1%>">添加</button></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        this.ui.searchBtn.click(function() {

            var params = CMS.kit.formatFormData(self.ui.form);

            $.extend(params, {
                'page': 1
            });
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.search(params);
            //}
            return false;
        });

        this.ui.results.delegate('.btnAdd', 'click', function(event) {
            var data = self.datas[parseInt($(this).data('num'))];
            self.onSelect(data);
        });
    },

    search: function(params, page) {
        var self, form;

        self = this;
        $.extend(params, {
            'page': page
        });
        form = new CMS.api.PojectSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);

                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setPage();
            }
        }

        form.shopSearch(params);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }

        this.ui.results.html(html.join('\n'));
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};

/**
 * 魔商店铺选择
 * @method CMS.plug.moShopStoreSearch
 */
CMS.plug.moShopStoreSearch = function(data) {

    var wrap = $(CMS.tmpl.MoShopStoreSearch);

    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        ok : wrap.find('.ok-btn'),
        searchBtn: wrap.find('.plug-shopstoreSearch-btn'),
        form: wrap.find('.plug-shopstoreSearch-form'),
        results: wrap.find('.plug-shopstoreSearch-results'),
        pageBox: wrap.find('.plug-shopstoreSearch-pagebox'),
        chkAllStore: wrap.find('#chkAllStore'),
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.mx_id = data.mx_id;
    this.regEvent();
    this.currPage = 1;
    //第一次默认请求
    this.ui.searchBtn.click();

    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.moShopStoreSearch.prototype = {
    tmpl: '<tr>\
                <td><input type="checkbox" class="store-id"  data-value="<%=_id.$id%>" name="store_ids[]" value="" /></td>\
                <td><%=num%></td>\
                <td><%=store_name%></td>\
                <td><%=store_type%></td>\
                <td><%=store_address%></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });
        //确定
        this.ui.ok.click(function() {
            var store_ids,array;
            array = [];
            $('.store-id').each(function(){
                this.checked && array.push($(this).data('value'));
            });

            if(array.length > 0){
                store_ids=array.join(',');
                var count=array.length;
                $("#sids").val(store_ids);
                $("#showChangeBox").html("<span id='hadSelected'>查看已选"+count+"家店铺</span>");
                self.ui.wrap.remove();
            }else{
                alert('至少要选中一个！');
            }
            return false;
        });
        //全选
        this.ui.chkAllStore.click(function(){
            var chk = this.checked;
            $('.store-id', self.ui.results).prop({'checked': chk});
        });
        this.ui.results.delegate('.store-id', 'click', function(){
            var checkboxes = self.ui.results.find('.store-id'), count = 0;
            checkboxes.each(function(){
                if(this.checked){
                    count++;
                }
            });
            if(count && count == checkboxes.length){
                self.ui.chkAllStore.prop('checked', true);
            }
        })

        this.ui.searchBtn.click(function() {
            var params = CMS.kit.formatFormData(self.ui.form);

            $.extend(params, {
                'page': 1,
                'mx_id': self.mx_id
            });
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.getStore(params);
            //}
            return false;
        });
    },

    getStore: function(params, page) {
        var self, form;

        self = this;
        $.extend(params, {
            'page': page,
            'mx_id': self.mx_id
        });
        form = new CMS.api.PojectSearchForm();
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);

                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setPage();
            }
        }

        form.getStore(params);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }
        var sids=($("#sids").val()).split(',');
        var $result = $(html.join('\n'));
        var seletedCount = 0;
        $result.find('input').each(function(i){
            var id = $(this).data('value');
            if($.inArray(id, sids) > -1){
                $(this).attr('checked', 'checked');
                seletedCount++;
            }
        });
        if(seletedCount == $result.find('input').length){
            this.ui.chkAllStore.prop('checked', true);
        } else {
            this.ui.chkAllStore.prop('checked', false);
        }
        this.ui.results.html('').append($result);
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                self.currPage = num;
                self.getStore(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};


/**
 * 魔商图片搜索(优惠卷添加)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.moShopPicSearch = function(data) {

    var wrap = $(CMS.tmpl.MoShopPicSearch);

    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        searchBtn: wrap.find('.plug-scratchSearch-btn'),
        form: wrap.find('.plug-scratchSearch-form'),
        results: wrap.find('.plug-scratchSearch-results'),
        pageBox: wrap.find('.plug-scratchSearch-pagebox')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.searchBtn.click();
		return false;
	});

    this.mx_id = data.mx_id;

    this.regEvent();
    this.currPage = 1;
     //第一次默认请求
    this.ui.searchBtn.click();
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.moShopPicSearch.prototype = {
    tmpl: '<tr>\
                <td><%=num%></td>\
                <td><img src="<%=image_url%>" style="max-height:120px; max-width:120px;"/></td>\
                <td><%=title%></td>\
                <td><button class="btn btn-small btnAdd" data-num="<%=num-1%>">添加</button></td>\
            </tr>',
    regEvent: function() {
        var self = this;
        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });

        this.ui.form.submit(function(){
            self.ui.searchBtn.click();
            return false;
        });
        this.ui.searchBtn.click(function() {

            var params = CMS.kit.formatFormData(self.ui.form);

            $.extend(params, {
                'page': 1,
                'mx_id': self.mx_id
            });
            //if( CMS.kit.isJSONAllValue( params ) ){
            self.search(params);
            //}
            return false;
        });

        this.ui.results.delegate('.btnAdd', 'click', function(event) {
            var data = self.datas[parseInt($(this).data('num'))];
            self.onSelect(data);
        });
    },

    search: function(params, page) {
        var self = this;
        var form = new CMS.api.PojectSearchForm();
        params.page = page;
        form.response = function(data) {
            if (data.result) {
                self.datas = data.data;
                self.setResultUi(self.datas);

                self.currPage = data.page;
                self.totalPage = data.page_count;
                self.setPage();
            }
        }

        form.shopPicSearch(params);
    },

    setResultUi: function(datas) {
        var html = [];
        var params = CMS.kit.formatFormData(this.ui.form);
        for (var i = 0; i < datas.length; i++) {
            datas[i] = $.extend(datas[i], {
                'num': i + 1,
                'type': params.type
            });
            html.push(CMS.kit.tmpl(this.tmpl, datas[i]));
        }

        this.ui.results.html(html.join('\n'));
    },

    setPage: function() {
        var self = this;
        if (this.totalPage > 1) {
            this.pageBox = new CMS.plug.Pagination(this.currPage, this.totalPage, this.ui.pageBox);
            this.pageBox.onPage = function(num) {
                var params = CMS.kit.formatFormData(self.ui.form);
                params.mx_id = self.mx_id;
                self.currPage = num;
                self.search(params, num);
            }
        } else {
            this.ui.pageBox.empty();
        }
    },

    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onSelect: function(data) {}
};

/**
 * 优惠卷系统生成(优惠卷添加)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.systemCreate = function(data) {

    var wrap = $(CMS.tmpl.systemCreate);

    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        btnCreate: wrap.find('.btnCreate'),
        form: wrap.find('.plug-systemCreate-form')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.btnCreate.click();
		return false;
	});


    this.regEvent();
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.systemCreate.prototype = {
    regEvent: function() {
        var self = this;

        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });

        this.ui.btnCreate.click(function() {
            self.create();
            return false;
        });
    },
    create: function() {
        var $input = this.ui.form.find('input');
        var num = parseInt($input.val());
        if(isNaN(num) || num < 1){
            num = 1;
            $input.val(num);
            $input.focus();
            return false;
        }
        var params = CMS.kit.formatFormData(this.ui.form);

        this.onSubmit(params);
    },
    onSubmit: function(params) {


    },
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};

/**
 * 优惠卷导入(优惠卷添加)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.sysLead = function(data) {

    var wrap = $(CMS.tmpl.systemLead);

    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        couponFile: wrap.find('.coupon-file'),
        coupFileUp: wrap.find('.coup-file-up'),
        btnLead: wrap.find('.btn-lead'),
        btnView: wrap.find('.btn-view'),
        btnAffirm: wrap.find('.btn-affirm'),
        form: wrap.find('.plug-systemLead-form'),
        systemLead: wrap.find('.system-lead'),
        systemLeadResult: wrap.find('.system-lead-result')
    };
	this.ui.form.submit(function(){
		return false;
	});


    this.regEvent();
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.sysLead.prototype = {
    regEvent: function() {
        var self = this;

        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });

        this.ui.btnView.click(function() {
            self.view();
            return false;
        });

        this.ui.btnAffirm.click(function() {
            self.affirm();
            return false;
        });

        this.ui.coupFileUp.change(function() {
            if(!/\.(?:csv|xls)$/.test(this.value)){
                alert('仅支持csv或xls文件');
                return false;
            }
            self.fileChange($(this));
        });

        this.ui.btnLead.click(function() {
            self.fileUp($(this));
            return false;
        });
    },
    affirm: function() {
        this.onDestroy();
        this.onAffirm();
    },
    view: function() {
        this.onView(this.error_data);
    },
    fileChange: function($this) {
        $this.context && this.ui.couponFile.val($this.context.value);
    },
    fileUp: function($this) {
        this.onFileUp(this.ui.coupFileUp);
    },
    onView: function() {

    },
    onFileUp: function() {

    },
    showResult: function(data) {
        this.error_data = data.error;
        this.ui.systemLead.addClass('hidden');
        this.ui.systemLeadResult.removeClass('hidden');
        this.ui.systemLeadResult.find('.sl-total').text(data.total);
        this.ui.systemLeadResult.find('.sl-error-total').text(data.error_total);
        this.ui.systemLeadResult.find('.sl-success-total').text(data.success_total);

    },
    onDestroy: function() {
        this.ui.wrap.remove();
    },
    onAffirm: function() {

    }
};

/**
 * 优惠卷导入(失败记录)
 * @method CMS.plug.ProjectSearch
 */
CMS.plug.sysLeadFail = function(data) {
    this.data = data;
};

CMS.plug.sysLeadFail.prototype = {
    tmpl: '<tr>\
            <td class="span1"><%=num %></td>\
            <td class="span2"><%=id %></td>\
            <td class="span2"><%=msg %></td>\
        </tr>',
    init: function() {
        var array, wrap, len;

        array = [];

        for (var i = 0, len = this.data.length; i < len; i++) {
            array.push(CMS.kit.tmpl(this.tmpl, $.extend(this.data[i], {
                'num': i + 1
            })));
        }

        wrap = $(CMS.kit.tmpl(CMS.tmpl.systemLeadFail, {
            'body': array.join(''),
            'num': len
        }));

        this.ui = {
            wrap: wrap,
            close: wrap.find('.close-btn'),
            btnView: wrap.find('.btn-view'),
            btnAffirm: wrap.find('.btn-affirm'),
            form: wrap.find('.plug-systemLeadFail-form')
        };
		this.ui.form.submit(function(){
			return false;
		});

        this.regEvent();
        this.ui.wrap.appendTo('body');
        this.ui.wrap.find('input:first').focus();
    },
    regEvent: function() {
        var self = this;

        this.ui.close.click(function() {
            self.ui.wrap.remove();
        return false;
        });

        this.ui.btnView.click(function() {
            self.view();
            return false;
        });

        this.ui.btnAffirm.click(function() {
            self.affirm();
            return false;
        });
    },
    affirm: function() {
        alert('hello world!');
    },
    view: function() {

    },
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};

/**
 * 审核不通过(强制下架)
 * @method CMS.plug.notApprove
 */
CMS.plug.notApprove = function(data) {

    var wrap = $(CMS.kit.tmpl(CMS.tmpl.notApprove, data));


    this.ui = {
        wrap: wrap,
        close: wrap.find('.close-btn'),
        btnSubmit: wrap.find('.btnSubmit'),
        btnCancel: wrap.find('.btnCancel'),
        form: wrap.find('.plug-notApprove-form')
    };
    var self = this;
	this.ui.form.submit(function(){
		self.ui.btnSubmit.click();
		return false;
	});


    this.regEvent();
    this.ui.wrap.appendTo('body');
    this.ui.wrap.find('input:first').focus();
};

CMS.plug.notApprove.prototype = {
    regEvent: function() {
        var self = this;

        this.ui.close.click(function() {
            self.ui.wrap.remove();
            return false;
        });

        this.ui.btnSubmit.click(function() {
            self.create();
            return false;
        });

        this.ui.btnCancel.click(function() {
            self.onDestroy();
            return false;
        });
    },
    create: function() {
        var params = CMS.kit.formatFormData(this.ui.form);

        this.onSubmit(params);
    },
    onSubmit: function(params) {


    },
    onDestroy: function() {
        this.ui.wrap.remove();
    }
};