<?php get_header(); ?>
<div class="wrap">
  <div class="container">
    <div class="row">
      <div class="twelve columns">
        <h4>Filter Programmes</h4>
      </div>
    </div><!-- end of row -->

    <div class="row">
      <div class="twelve columns">
        <div class="programs">
          <button class="filter-btn" data-filter="all">All</button>
          <button class="filter-btn" data-filter=".undergraduate">Undergraduate</button>
          <button class="filter-btn" data-filter=".graduate">Graduate</button>
          <button class="filter-btn" data-filter=".phd">PhD</button>
        </div>
      </div>
    </div><!-- end of row -->

    <div class="row">
      <div class="columns twelve">
        <h4>Sort Programmes</h4>
      </div>
    </div><!-- end of row -->

    <div class="row">
      <div class="columns twelve">
        <div class="programs">
          <button class="sort-btn" data-sort="default:asc">Default</button>
          <button class="sort-btn" data-sort="random">Random</button>
          <button class="sort-btn" data-sort="order:asc">Ascending</button>
          <button class="sort-btn" data-sort="year:desc order:desc">Descending<span class="multi">(Multi)</span></button>
        </div>
      </div>
    </div><!-- end of row -->

    <div class="row">
      <div class="twelve columns">
        <h4>Programmes List</h4>
      </div>
    </div><!-- end of row -->

    <div class="row">
      <div class="twelve columns">
        <ul class="courses" id="mix-wrapper">
          <li class="mix-target undergraduate" data-order="5" data-year="4"><a href="#">Economics<span>(U)</span></a></li>
          <li class="mix-target graduate" data-order="14" data-year="2"><a href="#">Pharmacology<span>(G)</span></a></li>
          <li class="mix-target graduate" data-order="7" data-year="1"><a href="#">Informatics<span>(G)</span></a></li>
          <li class="mix-target phd" data-order="4" data-year="3"><a href="#">Criminology<span>(P)</span></a></li>
          <li class="mix-target undergraduate" data-order="16" data-year="3"><a href="#">Sociology<span>(U)</span></a></li>
          <li class="mix-target undergraduate" data-order="6" data-year="4"><a href="#">Greek<span>(U)</span></a></li>
          <li class="mix-target phd" data-order="1" data-year="3"><a href="#">Astrophysics<span>(P)</span></a></li>
          <li class="mix-target undergraduate" data-order="12" data-year="4"><a href="#">Nursing<span>(U)</span></a></li>
          <li class="mix-target undergraduate" data-order="10" data-year="5"><a href="#">Microbiology<span>(U)</span></a></li>
          <li class="mix-target undergraduate" data-order="9" data-year="4"><a href="#">Mathematics<span>(U)</span></a></li>
          <li class="mix-target graduate" data-order="11" data-year="3"><a href="#">Nanoscience<span>(G)</span></a></li>
          <li class="mix-target phd" data-order="2" data-year="2"><a href="#">Biochemistry<span>(P)</span></a></li>
          <li class="mix-target phd" data-order="13" data-year="3"><a href="#">Pathology<span>(P)</span></a></li>
          <li class="mix-target graduate" data-order="8" data-year="1"><a href="#">Management<span>(G)</span></a></li>
          <li class="mix-target graduate" data-order="3" data-year="2"><a href="#">Biostatistics<span>(G)</span></a></li>
          <li class="mix-target phd" data-order="15" data-year="4"><a href="#">Public Health<span>(P)</span></a></li>
        </ul>
      </div>
    </div><!-- end of row -->   
  </div><!-- end of container -->
</div><!-- end of wrap -->
<script src="https://cdn.jsdelivr.net/jquery.mixitup/latest/jquery.mixitup.min.js"></script>
<script>jQuery('#mix-wrapper').mixItUp({
  load: {
  	sort: 'order:asc'
  },
	animation: {
    effects: 'fade rotateZ(-180deg)',
    duration: 700
  },
  selectors: {
    target: '.mix-target',
    filter: '.filter-btn',
    sort: '.sort-btn'
  },
  callbacks: {
    onMixEnd: function(state){
      console.log(state)
    }
  }
});</script>
