<div class="row row-offcanvas row-offcanvas-right">

  <div class="col-xs-12 col-sm-9">
    <div class="row">
      <?php foreach ($query->result() as $row): ?>
        <div class="col-6 col-sm-6 col-lg-4">
          <h2><?=$row->product_name?></h2>
          <p><?=$row->product_price?></p>
          <p><?=$row->product_description?></p>
          <a href="shop/add/<?=$row_product_id?>" class="btn btn-success">Add to Cart</a>
        </div>
      <?php endforeach ; ?>
    </div>
  </div>

  <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
    <div class="list-group">
      <a href="<?=base_url()?>" class="list-group-item">Categories</a>
      <?php foreach ($cat_query->result() as $row): ?>
        <a href="shop/index/<?=$row->cat_url_name?>" class="list-group-item"><?=$row->cat_name?></a>
      <?php endforeach; ?>
    </div>
  </div>
  
</div>
