/*
	Dialog
	Aerojin	
	2013.12.03
*/

CMS.plug = CMS.plug || {};

CMS.plug.Dialog = function(options){
	this.id = options.id || 'myModal' + new Date().getTime();
    this.title = options.title;
    this.body = options.body;
    this.edit = options.edit;
    this.container = options.container || 'body';
    this.backdrop = options.backdrop ? false : true;
};

CMS.plug.Dialog.prototype = {
	init: function(){

		this.ui = {};		
		this.ui.container = $(this.container);	
        var html = CMS.kit.tmpl(this.tmpl, { 
        	'id': this.id,
        	'title': this.title,
        	'body': this.body,
        	'edit': this.edit ? 'block' : 'none'
        });
        this.ui.container.append(html);        
        this.modal = $('#' + this.id);

        this.regEvent();
        this.open();
        this.onReady();
	},
	regEvent: function(){
		var _this = this;

		this.modal.on('hidden', function () {
            _this.onClose();
            $(this).remove();
        });

        this.modal.find('.btnClose').click(function () {
            _this.close();
        });

        this.modal.find('.btnSave').click(function () {
            _this.onUpdate();
        });
	},
    open: function () {
        var _this = this;
        this.modal.modal({
            backdrop: _this.backdrop,
            keyboard: true,
            show: true
        });
    },
    close: function () {
        this.modal.modal('hide');
    },
    onClose: function () {

    },
    onUpdate: function () {

    },
    onReady: function () {

    }
};

CMS.plug.Dialog.prototype.tmpl = '\
<div class="modal fade" id="<%=id%>" style="display:none;">\
    <div class="modal-header">\
        <a class="close" data-dismiss="modal">×</a>\
        <h3><%=title%></h3>\
    </div>\
    <div class="modal-body">\
       <%=body%>\
    </div>\
    <div class="modal-footer" style="display:<%=edit%>;">\
        <a href="javascript:void(0);" class="btn btnClose">关闭</a>\
        <a href="javascript:void(0);" class="btn btn-primary btnSave">保存更新</a>\
    </div>\
</div>';



/**
 * 子概率设置
 * @method CMS.plug.ProbabilitySet
 */
CMS.plug.ProbabilitySet = function( project_id, package_id, category_id , type){

    this.project_id = project_id;
    this.package_id = package_id;
    this.category_id = category_id;
    this.type = type;
    //this.type = type || false; //适用不同的请求

    var wrap = $( CMS.tmpl.probabilitySet );
    this.ui = {
        wrap: wrap,
        allAverageBtn: wrap.find('.plug-probabilitySet-allAverage'),
        leaveAverageBtn: wrap.find('.plug-probabilitySet-leaveAverage'),
        resetBtn: wrap.find('.plug-probabilitySet-reset'),
        saveBtn: wrap.find('.plug-probabilitySet-save'),
        result: wrap.find('.plug-probabilitySet-results'),
        inputs: wrap.find('.probability-input'),
        table: wrap.find('.plug-probabilitySet-table'),
        close: wrap.find('.close-btn')
    };

    $('body').append( this.ui.wrap );

    this.init();
    this.regEvent();

};

CMS.plug.ProbabilitySet.prototype = {
    itemTmpl: '<%var num=1; for(var i=0;i<datas.length;i++){ %>\
               <tr>\
                   <td><%=num %></td>\
                   <td><%=datas[i].item_name %></td>\
				   <td class="stamp_pic_col"><img style="max-width:50px;" src="<%=datas[i].pic %>" class="img-rounded"></td>\
                   <td><%=datas[i].quantity %></td>\
                   <td><%=datas[i].shaketime  %></td>\
				   <td><%=datas[i].repeatedly_times %></td>\
                   <td><input <%=datas[i].readonly ? "readonly":"" %> type="text" maxlength="7" value="<%=datas[i].shaketimelimit %>" class="span1 shaketimelimit-input" ></td>\
                   <td><input <%=datas[i].readonly ? "readonly":"" %> type="text" maxlength="7" value="<%=datas[i].shaketimeInterval %>" class="span1 shaketimeInterval-input" ></td>\
                   <td><input type="text" maxlength="3" value="<%=datas[i].probability %>" class="span1 probability-input" ></td>\
                </tr>\
                <% num++ } %>',

    init: function(){
        var self, from;
        self = this;
        form = new CMS.api.ProbabilityModel();
        form.response = function( data ){
            if( data.result ){
                self.datas = data.data;
                self.setResultUi( self.datas );
                self.serilData( self.datas );
				if(data.type != 2){
					$(".stamp_pic_col").hide();
				}
            }
        };
        if(self.type==1){
            form.getDatas( this.project_id,this.package_id, this.category_id );
        }else if(self.type==2){
            form.getScratch( this.project_id,this.package_id, this.category_id );
        }else if(self.type==3){
            form.getInvitationData( this.project_id,this.package_id, this.category_id )
        }
        //self.type ? form.getScratch( this.project_id,this.package_id, this.category_id ) : form.getDatas( this.project_id,this.package_id, this.category_id );
    },

    leaveCache:100,
    serilData: function( datas ){

        var cache = 0;
        for(var i=0,len=datas.length;i<len;i++){
            cache += datas[i].probability;
        }
        this.leaveCache = 100 - cache;
    },

    serilInput: function(){
         var inputs = this.ui.wrap.find('.probability-input');
         var len = inputs.length;

         var count = 0;
         for(var i=0;i<len;i++){
            if( /^(0|[1-9][0-9]*)$/.test( inputs[i].value ) ){
                console.log( $.trim( inputs[i].value ),parseInt( $.trim( inputs[i].value ),10 )  )
                count += parseInt( $.trim( inputs[i].value ),10 );
            }
         }
         this.leaveCache = 100 - count;
    },

    allAverage: function(){

       // var count = this.getAllInputsValue();
        var inputs = this.ui.wrap.find('.probability-input');
        var len = inputs.length;

        var average = parseInt( 100 / len, 10 );

            inputs.val( average );
    },

    leaveAverage: function(){

        var inputs = this.ui.wrap.find('.probability-input');
        var len = inputs.length;

        var noValue = [];
        for( var i=0;i<len;i++){
            if( $.trim( inputs[i].value ) == "" ){
                noValue.push( inputs[i] );
            }
        }

        var count = this.getAllInputsValue();
        var average = parseInt( ( 100 - count )/noValue.length,10 );

        $( noValue ).val( average );
    },

    getAllInputsValue: function(){

        var count = 0;
        var inputs = this.ui.wrap.find('.probability-input');
        var len = inputs.length;

        for(var i=0;i<len;i++){

            var value = inputs[i].value == "" ? 0 : inputs[i].value;
            count = count + parseInt(value,10);
        }


        return count;
    },

    formartData: function(){
        var params = [],
            quantity;

        var inputs = this.ui.wrap.find('.probability-input');
        var len = inputs.length;

        //基础次数
        var shaketimelimits=this.ui.wrap.find('.shaketimelimit-input');
        //间隔次数
        var shaketimeIntervals=this.ui.wrap.find('.shaketimeInterval-input');

        for( var i=0;i<this.datas.length;i++ ){

            quantity = $.trim( inputs[i].value ) == "" ? 0 : parseInt(inputs[i].value,10);
            shaketimelimit= $.trim( shaketimelimits[i].value ) == "" ? 0 : shaketimelimits[i].value;
            shaketimeInterval= $.trim( shaketimeIntervals[i].value ) == "" ? 0 : shaketimeIntervals[i].value;
            params.push({
                probability_id : this.datas[i]._id.$id,
                quantity : quantity,
                shaketimelimit : shaketimelimit,
                shaketimeInterval:shaketimeInterval
            });
        }

        return params;
    },

    regEvent: function(){
        var self = this;
        this.ui.close.click(function(){
            self.ui.wrap.remove();
        });

        this.ui.allAverageBtn.click(function(){
            self.allAverage();
        });

        this.ui.leaveAverageBtn.click(function(){
            self.leaveAverage();
        });

        this.ui.saveBtn.click(function(){
            var params = self.formartData();
            self.saveDatas( params );
        });

        this.ui.wrap.delegate('.probability-input','blur',function(){
            var $this = $(this);
            var value = parseInt( $this.val(),10 );
            if( $.trim( $this.val() ) != "" ){
                if( !/^[1-9][0-9]*$/.test( $.trim( $this.val() ) )  ){

                    alert('概率必须为正整数')
                    $this.val('');
                }
            }

            self.serilInput();

            if( self.leaveCache < 0 ){
                alert('概率总和已经超过100');
                $this.val('');
            }

            //console.log('leaveCache----',self.leaveCache)
        });
    },

    setResultUi: function( datas ){

        this.ui.result.empty();
        var html = CMS.kit.tmpl( this.itemTmpl,{datas:datas});
        this.ui.table.removeClass('hidden');
        this.ui.result.html( html );

    },

    saveDatas: function( params ){
        var self = this;
        var form = new CMS.api.ProbabilityModel();
            form.response = function( data ){
                if( data.result ){
                    console.log('saveDatas');
                   self.onSave();
                }else{
                    alert( data.msg );
                }
            }
        if(self.type==1){
            form.saveDatas( {category_probability:params} );
        }else if(self.type==2){
            form.saveScratch( {category_probability:params} );
        }else if(self.type==3){
            form.saveInvitation({category_probability:params})
        }
        //this.type ? form.saveScratch( {category_probability:params} ) : form.saveDatas( {category_probability:params} );
    },

    destroy: function(){
        this.ui.wrap.remove();
    },

    onDestroy: function(){
        this.destroy();
    },

    onSave: function(){

    }
};


/**
 * 全选和不全选
 * @method CMS.plug.CheckBoxSelect
 * @param {jQDom} btn 触发的按钮
 * @param {jQDom} tab 包含input[type=checkbox]父级dom
 */
CMS.plug.CheckBoxSelect = function( btn,tab ){

    var length = arguments.length;

    this.selectType = 0;

    this.ui = {
        btn: btn,
        tab: tab,
        checkboxs: tab.find('input[type=checkbox]')
    };

    this.regEvent();
};

CMS.plug.CheckBoxSelect.prototype = {

    regEvent: function(){
        var self = this;
        this.ui.btn.click(function(){
            if( self.selectType ){
                self.selectType = 0;
                self.ui.checkboxs.prop('checked', false);
            }else{
                self.selectType = 1;
                self.ui.checkboxs.prop('checked', true);
            }
        });
    }
};
