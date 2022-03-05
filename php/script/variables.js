/*
 * Proměnné patřící k projektu, jejich prvnotní naplnění a inicializace
 * Projekt: STORIES
 * Vytvořil: Janek
 */

/*proměnné objektů ve stránce*/
var back;
var left;
var logo;
var content_menu;
var function_buttons_left
var middle;
var top_menu;
var content;
var content_footer;
var right;
var user;
var right_content;
var function_buttons_right;

/*proměnné poměrů velikostí objěktů ve stránce*/
/*left_ratio + middle_ratio + right_ratio = 1*/
/*top_menu_ratio + main_ratio = 1*/
var left_ratio = 1/5;
var middle_ratio = 3/5;
var right_ratio = 1/5;
var top_ratio = 1/5;
var content_ratio = 3/5;
var bottom_ratio = 1/5;

/*proměnné velikosti dokumentu*/
var height;
var width;

/*Proměnné pro komunikaci s ostatními servery*/
var xhttp = new XMLHttpRequest();
var my_address;

/*Definování inventáře a jeho střev*/
var inventory;
function item(itemId, itemCount, itemName, itemProperties){
  this.name = itemName;
  this.count = itemCount;
  this.id = itemId;
  this.properties = itemProperties;

  /*přidávání a odebírání předmětů z inventáře*/
  this.modify = function(itemCount) {
    if (this.count + itemCount>=0) {
      this.count = this.count + itemCount;
      return "item modified";
    }else{
      return "insufficient number of items";
    }
  }
}
function inventories(){
  this.itemCount = 0;
  this.items;
  /*Přidávání předmětů do inventáře...*/
  this.modifyItem = function(itemId, itemCount, itemName, itemProperties){
    var inventoryItemId = -1;
    inventoryItemId = findItem(itemId);
    if (itemId === -1){
      if (itemCount>=0) {
        this.items[itemCount] = new item(itemId, itemCount, itemName, itemProperties);
        itemCount = itemCount + 1;
        return "item modified";
      }else{
        return "insufficient number of items";
      }
    } else{
      var pomString = this.items[inventoryItemId].modify(itemCount);
      return pomString;
    }
  }
  /*Hledání předmětů*/
  this.findItem = function(itemId) {
    for (let i = 0; i < itemCount; i++) {
      if (items[i].id = itemId){
        return i;
      }
    }
    return -1;
  }
}

/*Definovaní soupisky příběhů a funkcí s nimi spojených*/
function story(data){
  this.name = (data[1].split(":"))[1];
  this.id = (data[0].split(":"))[1];
}
function stories(){
  this.story;

  

}

/*definování paragrafů a funkcí s nimi spojených*/

/*prvotní doplnění proměnných*/
function onLoadVar() {
  back = document.getElementById('back');

  left = document.getElementById('left');
  logo = document.getElementById('logo');
  content_menu = document.getElementById('content_menu');
  function_buttons_left = document.getElementById('function_buttons_left');

  middle = document.getElementById('middle');
  top_menu = document.getElementById('top_menu');
  content = document.getElementById('content');
  content_footer = document.getElementById('content_footer');

  right = document.getElementById('right');
  user = document.getElementById('user');
  right_content = document.getElementById('right_content');
  function_buttons_right = document.getElementById('function_buttons_right');

  height = window.innerHeight;
  width = window.innerWidth;

  if (window.location.hostname === "localhost") {/*Pokud přistupuji ze serveru localhost*/
    my_address = "https://localhost/příběhy/";
  }else{
    my_address = "https://www.stories.ents.cz/";
  }

  inventory = new inventories();
}
