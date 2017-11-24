/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var canvas = document.getElementById("myCanvasNet");
var netview = $("#netview").text();
var center = 400;
var maxArea = 14;
var radius = 0;
var MinRadius = 50;
var MaxRadius= 0;
var lastItem;
var imgUrls = [];
var img = [];
var CanvasWidth = 320;
var CanvasHeight = 200;
var CanvasCenter= {
  x: 160,
  y: 100
};
var networkG = jQuery.parseJSON(netview);
var ctx = canvas.getContext("2d");

var scrollcanvas = $('#canvas-net');
scrollcanvas.scrollTop(250);

var a = $('#profileimg').attr('src');

if(a !== "false"){
    this.imgUrls[0] = $('#profileimg').attr('src');
    console.log(this.imgUrls[0]);
}
else{
    this.imgUrls[0] = $('#img1').attr('src');
}

this.imgUrls[1] = $('#img2').attr('src');
this.imgUrls[2] = $('#img3').attr('src');
this.imgUrls[3] = $('#img4').attr('src');
this.imgUrls[4] = $('#img5').attr('src');

for(var i=0; i<Object.keys(this.imgUrls).length; i++){
    this.img[i] = new Image();
    this.img[i].src = this.imgUrls[i];
}

//console.log(Object.keys(this.imgUrls).length);
//drawRadius.call();

$(document).ready(function(){
   
   if(Object.keys(networkG.result).length > 0){
        defineMaxRadius();
      }
    
});

function defineMaxRadius(){
    this.lastItem = networkG.result[Object.keys(networkG.result).length-1];
    for(var i=1; i < this.lastItem.level + 1; i++){
      this.MaxRadius = this.MaxRadius + this.MinRadius - 2 * i;
    }
    setTimeout(()=>{ defineSizeCanvas() }, 100 );
}

function defineSizeCanvas(){
    this.CanvasWidth = ( this.CanvasWidth > (this.MaxRadius*2) + 100 ) ? this.CanvasWidth : (this.MaxRadius*2) + 100;
    this.CanvasHeight = ( this.CanvasHeight > (this.MaxRadius*2) + 100 ) ? this.CanvasHeight : (this.MaxRadius*2) + 100;
    setTimeout(()=>{ defineCenterCanvas() }, 100 );
}

function defineCenterCanvas(){
    this.CanvasCenter.x = ( CanvasCenter.x > ( this.CanvasWidth/2 ) ) ? CanvasCenter.x : ( this.CanvasWidth/2 ); 
    this.CanvasCenter.y = ( CanvasCenter.y > ( this.CanvasHeight/2 ) ) ? CanvasCenter.y : ( this.CanvasHeight/2 );
setTimeout(()=>{ startDrawCanvas() }, 100 );
}

function startDrawCanvas(){
    ctx.canvas.width = this.CanvasWidth;
    ctx.canvas.height = this.CanvasHeight;
    setTimeout(()=>{ drawNetworkG() }, 100 );
}

function drawNetworkG(){
    var radiusImage = 18;
    drawImage(radiusImage, CanvasCenter.x, CanvasCenter.y, this.imgUrls[0]);
    var countPerson;
    for( var i = 1; i <= this.lastItem.level; i++){
      countPerson = 0;
      radiusImage = (radiusImage <= 4) ? 4 : radiusImage - 2;
      this.radius = this.radius + this.MinRadius - 2*i;
      drawRadius(this.radius);
      var points = countPointsByLevel(i);
      var angulo = 360 / points;
      for( var j=0; j < Object.keys(networkG.result).length ; j++ ){
        if(this.networkG.result[j].level == i){
          this.networkG.result[j].coordenades = calculatePoint(this.radius, angulo*j);
          this.networkG.result[j].radiusImage = radiusImage;
          this.networkG.result[j].radius = this.radius;
          drawImage(radiusImage, this.networkG.result[j].coordenades.x, this.networkG.result[j].coordenades.y, networkG.result[j].img)
        }
      }
    }
  }

function countPointsByLevel(level){
    var countPerson = 0;
    for( var j=0; j < Object.keys(networkG.result).length ; j++ ){
      countPerson = ( networkG.result[j].level == level ) ? countPerson + 1 : countPerson;
    }
    return countPerson;
  }
  
  function calculatePoint(radio, pointAngle){
    var result;
    result = Object.assign(
      { 
        x: ((radio * (Math.round((Math.cos( pointAngle * Math.PI / 180))*1000)/1000))+CanvasCenter.x),
        y: ((radio * (Math.round((Math.sin( pointAngle * Math.PI / 180))*1000)/1000))+CanvasCenter.y)
      }
    );
    return result;
  }
  
  function drawImage(radius, x, y, imgProfile) {
    ctx.save();
    ctx.beginPath();
    ctx.strokeStyle = "#FFF";
    ctx.arc(x, y, radius, 0, 2 * Math.PI, false);
    ctx.lineWidth = radius * 0.5;
    ctx.stroke();
    ctx.clip();
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, 2 * Math.PI, false);
    if(imgProfile == false){
      var quadrant = calculateQuadrantForImage(x, y);
      ctx.drawImage(this.img[quadrant], 0, 0, this.img[quadrant].width, this.img[quadrant].height, x - radius, y - radius, radius*2, radius*2);
    }
    else {
      var image = new Image();
      image.src = imgProfile;
      image.onload = function(){
        ctx.drawImage(image, 0, 0, image.width, image.height, x - radius, y - radius, radius*2, radius*2);
      }
    }
    ctx.restore();
  }
  
function calculateQuadrantForImage(x, y){
    return (x == CanvasCenter.x && y == CanvasCenter.y) ? 0 : (x > CanvasCenter.x) ? ((y > CanvasCenter.y) ? 1 : 4) : ((y > CanvasCenter.y) ? 2 : 3);
  }    

function drawRadius(radius){
    ctx.beginPath();
    ctx.arc(this.CanvasCenter.x, this.CanvasCenter.y, radius, 0, 2*Math.PI, false);
    ctx.strokeStyle = '#828282';
    ctx.lineWidth = 0.5;
    ctx.stroke();
}
 
function clickCricle(){
    /*var distanceMin = 10;
    var distance;
    let point:any = false;
    if(Math.sqrt( ((ev.layerX-this.CanvasCenter.x)*(ev.layerX-this.CanvasCenter.x))+((ev.layerY-this.CanvasCenter.y)*(ev.layerY-this.CanvasCenter.y)) ) < distanceMin){
      this.navCtrl.push(MorePage);
    }
    else {
      for (var i=0; i < Object.keys(this.networkG).length ; i++){
        distance = Math.sqrt( ((ev.layerX-this.networkG[i].coordenades.x)*(ev.layerX-this.networkG[i].coordenades.x))+((ev.layerY-this.networkG[i].coordenades.y)*(ev.layerY-this.networkG[i].coordenades.y)) );
        if( distanceMin > distance ){
          distanceMin = distance;
          point = this.networkG[i];
        }
        if(i == Object.keys(this.networkG).length-1 && point != false){
          this.openCustomerGId(point);
        }
      }
    }*/
 }

$(function() {
    $(".pendingsinvitation").fancybox();

    // search member
    $('.searchimg').click( function() {
        $('#formnetwork').submit();
    });

    // popup message
    $('.myfancybox').click( function() {
        $("#idsendmessage").val("");
        $("#idreceivemessage").val("");
        $("#messagesendmessage").val("");
        var data = $(this).attr('send').split('|');
        $("#idreceivemessage").val(data[0]);
        $("#namesendmessage").text(data[1]);
        $("#imgsendmessage").attr("src", data[2]);
        $("#idsendmessage").val(data[3]);
    });

    // send message
    $('#buttonsendmessage').click( function() {
        var idsend = $("#idsendmessage").val();
        var idreceive = $("#idreceivemessage").val();
        var message = $("#messagesendmessage").val();
        var jsSrcRegex = /([^\s])/;
        if ( idsend != "" && idreceive != "" && message != "" && jsSrcRegex.exec(message) ) {
            $.ajax({
                method:"POST",
                data: {
                    'action': 'sendmessage',
                    'idsend': idsend,
                    'idreceive': idreceive,
                    'message': message
                },
                url: '/messagesponsor.php', 
                success:function(response){
                    alert("Mensaje enviado exitosamente.");
                    $("#idsendmessage").val("");
                    $("#messagesendmessage").val("");
                    $.fancybox.close();
                    location.reload();
                }
            });
        }
    });
});