/* RFID Lock Project 
   Copyright (C) 2013  Ben Barker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
function addUser(){
	var fn= $("#fn").val();
	var ln= $("#ln").val();
	var code= $("#code").val();

	if(checkcode(code) && checkname(fn) && checkname(ln)){
		//alert(fn+" "+ln+" "+code);
	
		$.ajax({
                	url: "ajax/add.php?fn="+fn+"&ln="+ln+"&code="+code
	        }).done(function() {
        	        alert("User added");
                	window.location.reload();
	        });
	}
}

function checkname(name){
	if($.trim(name)==""){
		alert("Name field cannot be empty");
		return false;
	}
	if( /[^a-zA-Z )(]/.test(name)){
		alert('Name fields can contain only letters');
	return false;
	}
	return true;     
}

function checkcode(code){
	if($.trim(code)==""){
                alert("Code field cannot be empty");
                return false;
        }

        if( /[^0-9A-Z]/.test(code)){
                alert('Code can contain only numbers');
        return false;
        }
        return true;     
}

function deleteUser(id){
	$.ajax({
                url: "ajax/delete.php?id="+id
        }).done(function() {
		alert("User deleted");
        	window.location.reload();
	});
}

function toggleUser(id,state){
	if(state==1){
	        $.ajax({
        	        url: "ajax/toggle.php?id="+id+"&state="+state
	        }).done(function() {
                	//alert("User Reactivated");
	                window.location.reload();
        	});
	}else{
		$.ajax({
                        url: "ajax/toggle.php?id="+id+"&state="+state
                }).done(function() {
                        //alert("User Suspended");
                        window.location.reload();
                });
	}
}


function openDoor(){
	$("#toggle").attr("disabled",true);
	$('*').css( 'cursor', 'progress' );
	$.ajax({
		url: "ajax/open.php"
	}).done(function() {
		$("#toggle").attr("disabled",false);
		$('*').css( 'cursor', 'default' );
	});
}

