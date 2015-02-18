

/**
* Accept timestamp or Date object [dd-mm-yyyy hh:mm:ss]
**/
simplify.filter('tsDateTime', function() {
  return function(input) {
    var dt; 
    if(input instanceof Date){
      dt = input;
    }else{
      if(typeof input ==='number'){
        dt = new Date(input*1000);
      }else{
        dt = new Date(input);
      }
    }
    var str = dt.toLocaleString();
    return str.substr(0,str.length-3);
  };
});

/**
* Accept timestamp or Date object *Simple Version (just dd-mm-yyyy)*
**/

simplify.filter('tsDate', function() {

  return function(input) {

    var dt;

    if(input instanceof Date){

      dt = input;

    }else{

      if(typeof input ==='number'){

        dt = new Date(input*1000);

      }else{

        dt = new Date(input);

      }
    }
    var str = dt.toLocaleString();
    return str.substr(0,str.length-9);
  };
});
