

/**
* Accept timestamp or Date object [dd-mm-yyyy hh:mm:ss] DOESNT WORK WITH FIREFOX OR IE
**/
simplify.filter('tsDateTime', function() {

  return function(input) {
    if(navigator.appVersion.indexOf("Chrome")==-1 && input != undefined){

      var dt;
      console.log('input ' + input);
      dt = input.substr(0, 19);
      console.log('dt 1' + dt);
      dt = dt.toLocaleString().toString();
      dt = dt.split(' ');
      date = dt[0].split('-');
      time = dt[1];
      dt = date[2]+'/'+date[1]+'/'+date[0]+' '+time;
      console.log('dt 2 ' + dt);
      return dt;
      
    }
    else if (input != undefined) {
      var dt;
      // console.log('input ' + input);
      dt = new Date(input);
      // console.log('dt ' + dt);
      dt = dt.toLocaleString().toString().split('/');
      // console.log('dt ' + dt);
      if (dt[1] < 10){
        dt[1]='0'+dt[1];
      }
      if (dt[0] < 10){
        dt[0]='0'+dt[0];
      }
      dt = dt[0]+'/'+dt[1]+'/'+dt[2];
      // console.log('dt ' + dt);
      return dt;
    };
  };
});
/**
* Accept timestamp or Date object [dd-mm-yyyy hh:mm:ss] DOESNT WORK WITH FIREFOX OR IE
**/
simplify.filter('tsTime', function() {
  return function(input) {
    var dt; 
    var timestamp; 
    if(input instanceof Date){
      dt = input;
    }else{
      if(typeof input ==='number'){
        dt = new Date(input*1000);
      }else{
        dt = new Date(input);
      }
    }
    var timestamp = new Date(dt).getTime();
    //we add time difference (+11h)
    timestamp = timestamp /1000;
    var str = new Date((timestamp+39600)*1000);
    str = str.toLocaleString().toString();
    str = str.substr(8, str.length-8);
    // console.log('str ' + str);
    return str;
  };
});
/**
* Accept timestamp or Date object [dd-mm-yyyy hh:mm:ss] DOESNT WORK WITH FIREFOX OR IE
**/
simplify.filter('range', function() {
  return function(input, min, max) {
    min = parseInt(min); //Make string input int
    max = parseInt(max);
    for (var i=min; i<max; i++)
      input.push(i);
    return input;
  };
});

/**
* Accept timestamp or Date object [dd-mm-yyyy hh:mm:ss] DOESNT WORK WITH FIREFOX OR IE
**/
simplify.filter('tsTimeLocal', function() {
  return function(input) {
    var dt; 
    var timestamp; 
    if(input instanceof Date){
      dt = input;
    }else{
      if(typeof input ==='number'){
        dt = new Date(input*1000);
      }else{
        dt = new Date(input);
      }
    }
    var timestamp = new Date(dt).getTime();
    //we add time difference (+11h)
    var str = new Date(timestamp);
    str = str.toLocaleString().toString();
    str = str.substr(8, str.length-8);
    // console.log('str ' + str);
    return str;
  };
});

/**
* Accept timestamp or Date object *Simple Version (just dd-mm-yyyy)* DOESNT WORK WITH FIREFOX OR IE
**/

simplify.filter('tsDate', function() {

  return function(input) {
    if(navigator.appVersion.indexOf("Chrome")==-1 && input != undefined){

      var dt;
      console.log('input ' + input);
      dt = input.substr(0, 10);
      console.log('dt 1' + dt);
      dt = dt.toLocaleString().toString().split('-');
      dt = dt[2]+'/'+dt[1]+'/'+dt[0];
      console.log('dt 2 ' + dt);
      return dt;
    }
    else if (input !=undefined){
      
      var dt;
      dt = new Date(input).toLocaleString().toString();
      dt = dt.substr(0,dt.length-9).split('/');
      if (dt[1] < 10){
        dt[1]='0'+dt[1];
      }
      if (dt[0] < 10){
        dt[0]='0'+dt[0];
      }
      dt = dt[0]+'/'+dt[1]+'/'+dt[2];
      // console.log('dt : ', dt);

      return dt;

    }
  };
});

/**
* Accept numeric , convert decimal hours to XX h XX mn format
**/

simplify.filter('hourConv', function() {

  return function(input) {
    if (input != undefined) {
      var result;
      var minutes;
      var split;
      split = input.toString().split('.');

      result = split[0]+' h ';
      minutes = Number('0.'+split[1]);
      if (isNaN(minutes) != true) {
        result = result+Math.round(minutes*60)+' mn';
    }; 
      return result;  
    };
  };
});
