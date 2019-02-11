/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
var myglobal='';
$(document).ready(function() {
    $('.eqLogicAttr[data-l1key=configuration][data-l2key=typeN]').on('change', function () {
		if($('.li_eqLogic.active').attr('data-eqlogic_id') != '' && $(this).value() != ''){
			//$('#div_alert').showAlert({message: ">>"+$(this).value()+"<<", level: 'danger'});
			//$('#div_alert').showAlert({message: ">>Global:<<"+myglobal+">>Value:<<"+$(this).value(), level: 'danger'});	
			getModel($(this).value(),$('.li_eqLogic.active').attr('data-eqlogic_id'));
			if (myglobal == '' || myglobal == $(this).value()) {
				//$('#div_alert').showAlert({message: ">>Change:<<"+$(this).value(), level: 'danger'});
				getDevices($(this).value(),$('.li_eqLogic.active').attr('data-eqlogic_id'),true);
			}
			else {
				//$('#div_alert').showAlert({message: ">>not:<<"+$(this).value(), level: 'danger'});
				getDevices($(this).value(),$('.li_eqLogic.active').attr('data-eqlogic_id'),false);
			}
			//$('#div_alert').showAlert({message: ">>Change:<<"+$(this).value(), level: 'danger'});				
			//getDevices($(this).value(),$('.li_eqLogic.active').attr('data-eqlogic_id'));
			$('#img_type').attr("src", 'plugins/wifilightV2/core/config/images/icon'+$(this).value()+'.png');
			myglobal = $(this).value();

		}else{
			$('#img_type').attr("src",'plugins/wifilightV2/core/config/images/wifilightV2_icon.png');
			$('#img_subtype').attr("src",'plugins/wifilightV2/core/config/images/wifilightV2_icon.png');
			//$('#div_alert').showAlert({message: ">>vide<<", level: 'danger'});
			$(".subtype").hide();
			$(".canal").hide();
          	$(".macad2").hide();
			$(".identifiant").hide();
			$(".nbLeds").hide();
			$(".colorOrder").hide();
		}  
	});

	$('.eqLogicAttr[data-l1key=configuration][data-l2key=subtype]').on('change', function () {
		if($(this).value() != '' && $(this).value() != null){
			$('#img_subtype').attr("src", 'plugins/wifilightV2/core/config/images/icon'+$(this).value()+'.png');
			getModel($(this).value(),$('.li_eqLogic.active').attr('data-eqlogic_id'));
			myglobal = '';
			//if (model['canal']>1) $(".canal").show();
			//else $(".canal").hide();
			
    }
});

});
 
 
$(function() {
    $("#table_cmd tbody").delegate(".listCmdwifilightV2", 'click', function(event) {
        $('.description').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $('#sel_addPreConfigCmdwifilightV2').value()).show();
        $('.version.' + $('#sel_addPreConfigCmdwifilightV2').value()).show();
        $('.required.' + $('#sel_addPreConfigCmdwifilightV2').value()).show();
        $('#md_addPreConfigCmdwifilightV2').modal('show');
        $('#bt_addPreConfigCmdwifilightV2Save').undelegate().unbind();
        var tr = $(this).closest('tr');
        $("#div_mainContainer").delegate("#bt_addPreConfigCmdwifilightV2Save", 'click', function(event) {
            var cmd_data = json_decode($('.json_cmd.' + $('#sel_addPreConfigCmdwifilightV2').value()).value());
            tr.setValues(cmd_data, '.cmdAttr');
            $('#md_addPreConfigCmdwifilightV2').modal('hide');
        });
    });

    $("#sel_addPreConfigCmdwifilightV2").on('change', function(event) {
        $('.description').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $(this).value()).show();
        $('.version.' + $(this).value()).show();
        $('.required.' + $(this).value()).show();
    });

    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
});
function getModel(_conf,_id) {
   $.ajax({
        type: "POST", 
        url: "plugins/wifilightV2/core/ajax/wifilightV2.ajax.php", 
        data: {
            action: "getModel",
            conf: _conf,
            id: _id,
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
            }
			if (data.result!=null) {
				//$('#div_alert').showAlert({message: ">>"+data.result['macad']+"<<", selected: 'danger'});
				if (data.result['canal']>1) $(".canal").show();
				else $(".canal").hide();
				if (data.result['macad'] == 1) $(".macad2").show();
				else $(".macad2").hide();
				if (data.result['identifiant'] == 1) $(".identifiant").show();
				else $(".identifiant").hide();
				if (data.result['colorOrder'] == 1) $(".colorOrder").show();
				else $(".colorOrder").hide();
				if (data.result['nbLeds'] == 1) $(".nbLeds").show();
				else $(".nbLeds").hide();
				//$(".macad").show();
				//$(".canal").show();
				//$(".canal2").show();
			}
			else
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
        }
    });
}

$('.eqLogicAttr[data-l1key=configuration][data-l2key=typeN]').on('change', function () {
    var instruction = $('.eqLogicAttr[data-l1key=configuration][data-l2key=typeN] option:selected').attr('data-instruction');
    $('#div_instruction').empty();
    if(instruction != '' && instruction != undefined){
       $('#div_instruction').html('<div class="alert alert-info">'+instruction+'</div>');
   }
});
function getDevices(_conf,_id,_mark) {
	
    $.ajax({
        type: "POST", 
        url: "plugins/wifilightV2/core/ajax/wifilightV2.ajax.php", 
        data: {
            action: "getDevices",
            conf: _conf,
            id: _id,
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
			//$('#div_alert').showAlert({message: "input"+_mark, level: 'danger'});
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var options = '';
			var options2 = '';
			var tab=new Array(); 
			if (data.result!=null) {
				/*
				for (var i in data.result) {
					if (data.result[i]['selected'] == 1){
						//$('#div_alert').showAlert({message: ">>"+i+"<<", selected: 'danger'});
						options += '<option value="'+i+'" selected>'+data.result[i]['name']+'</option>';
						if (data.result[i]['canal']>1) $(".canal").show();
						else $(".canal").hide();
						//if (data.result[i]['macad'] == 1) $(".macad2").show();
						//else $(".macad2").hide();
						//$('#div_alert').showAlert({message: ">>"+data.result[i]['canal']+"<<", selected: 'danger'});
												
					} else {
						options += '<option value="'+i+'">'+data.result[i]['name']+'</option>';
					}
				}
				*/
				var selected = 0;
				for (var i in data.result) {
					if (data.result[i]['selected'] == 1 && (_mark == true)){
						//$('#div_alert').showAlert({message: ">>Sel:"+i+"<<", selected: 'danger'});
						tab[data.result[i]['name']] = '<option value="'+i+'" selected>'+data.result[i]['name']+'</option>';		
						selected = i;						
					} 
					else {
						//$('#div_alert').showAlert({message: ">>Not:"+i+"<<", selected: 'danger'});
						tab[data.result[i]['name']] ='<option value="'+i+'">'+data.result[i]['name']+'</option>';
					}
					
				}
				//tab.sort();
				var res=new Array();
				for ( var n in tab ) {
					res.push([n, tab[n]]);
				}
				res.sort(function(a, b) {
					a = a[0].toUpperCase();
					b = b[0].toUpperCase();

					return a < b ? -1 : (a > b ? 1 : 0);
				});				
				for (var index in res) {
					options += res[index][1];
				}
								
				if (options !='') {
					if (_mark != false)
						options = '<option value="">{{Aucun}}</option>'+options;
					else
						options = '<option value="" selected>{{Aucun}}</option>'+options;
					$(".subtype").show();
					$(".listModel").html(options);

					// show optionnal configuration for the choosen device
					if (_mark != false) {
						icon = $('.eqLogicAttr[data-l1key=configuration][data-l2key=subtype]').value();
						if(icon != '' && icon != null){
							$('#img_subtype').attr("src", 'plugins/wifilightV2/core/config/images/icon'+icon+".png");
						}
						if (data.result[selected]['canal']>1) $(".canal").show();
						else $(".canal").hide();
						if (data.result[selected]['macad'] == 1) $(".macad2").show();
						else $(".macad2").hide();
						if (data.result[selected]['identifiant'] == 1) $(".identifiant").show();
						else $(".identifiant").hide();
						if (data.result[selected]['colorOrder'] == 1) $(".colorOrder").show();
						else $(".colorOrder").hide();
						if (data.result[selected]['nbLeds'] == 1) $(".nbLeds").show();
						else $(".nbLeds").hide();
					}
					else {
						$('#img_subtype').attr("src",'plugins/wifilightV2/core/config/images/wifilightV2_icon.png');
						$(".canal").hide();
						$(".macad2").hide();
						$(".identifiant").hide();
						$(".colorOrder").hide();
						$(".nbLeds").hide();
					}
				} else {
					$(".subtype").hide();
				}
			}
			else
				$(".subtype").hide(); 
          
        }
    });
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}  };
    }

	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="row">';
	tr += '<div class="col-lg-6">';
	tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icone</a>';
	tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
	tr += '</div>';
	tr += '<div class="col-lg-6">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';	
	tr += '</div>';
	tr += '</div>';
	tr += '</td>';
	
	
	
	
    tr += '<td class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType();
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span></td>';
    tr += '<td ><input class="cmdAttr form-control input-sm" data-l1key="logicalId" style="margin-top : 5px;" />';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parameters" style="margin-top : 5px;" placeholder="Paramètres" ></input>';
    tr += '</td>';
	
	
	tr += '<td>';
	tr += '<span><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" data-size="mini" data-label-text="Afficher" checked/></span>';       
    tr += '</td>';
	
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
	tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> Tester</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}