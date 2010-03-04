<?php slot('inline_styles') ?>
.tabular tr.selected td { background:#FDF9D2; color:#9B9878; }
<?php end_slot() ?>

<p>SelectionTable extends Core.Ui.AjaxTable and adds a row selection feature.</p>

<div id="demo">
  <?php include_partial('ajaxtable', array('selection' => true)) ?>
</div>

<h2>Usage</h2>

<p>Instance the AjaxTable, passing the container element</p>

<?php pre_start('js') ?>
Core.ready(function() {
  var t = new Core.Ui.SelectionTable(Core.Ui.get('SelectWhateverComponent'));
});
<?php pre_end() ?>

<p>Output table html as for AjaxTable, but each row has an additional column:</p>

<?php pre_start('html') ?>
&lt;td>
&lt;/td>
<?php pre_end() ?>

<script type="text/javascript">
Core.ready(function() {
  var t = new Core.Widgets.SelectionTable(Core.Ui.get('demo'));
});

/* yuI3 OOP
 * 
 */
/*
Person = Core.createClass();

Person.prototype = {
  init: function(name)
  {
    this.name = name;
    console.log("Person.init(%o)", arguments);
  },
  say: function(s)
  {
    console.log("Person.say(%s)", s);
  }
};


Alien = Core.extend(Person, 
{
  init: function(name, race)
  { 
    this.race = race;
    Alien.superclass.init.apply(this, [name]);
    console.log("Alien.init(%o)", arguments);
  },

  say: function(s) {
    Alien.superclass.say.call(this, "hi");
    console.log("Alien.say(%s)", s);
  }
});

Foo = Core.extend(Alien, { init:function(name, race, color){ console.log("I r a %s foo", color); Foo.superclass.init.call(this, name, race) } });
// f = new Foo("john", "Johnny", "red")

p = new Person("Human");
p2 = new Alien("Zorlgub", "Goblin");

Bird = function (name) {
    this.name = name;
};
 
Bird.prototype.flighted   = true;  // Default for all Birds
Bird.prototype.isFlighted = function () { return this.flighted };
Bird.prototype.getName    = function () { return this.name };
 
Chicken = function (name) {
    // Chain the constructors
    Chicken.superclass.constructor.call(this, name);
};
// Chickens are birds
Y.extend(Chicken, Bird);
 
// Define the Chicken prototype methods/members
Chicken.prototype.flighted = false; // Override default for all Chickens
*/
</script>
