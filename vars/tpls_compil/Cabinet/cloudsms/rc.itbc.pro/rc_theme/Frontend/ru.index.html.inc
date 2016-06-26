<? $this->md5_compil='593e662edd488f0ac19b575670c0411d'; ?>
<div class="mainContentBlock mainInfo">

  <div class="left">
    <span>
      Приветствуем вас в нашем интернет-магазине крепежных изделий. Среди широкого ассортимента товаров вы сможете легко найти то, что нужно, узнать о наличии на складе, сделать заказ, оплатить покупку и <br>оформить доставку.  
    </span>
    <div class="actions">
      <a href="/about/" class="aboutLink">О магазине</a>
      <a href="/faq/" class="helpLink">Помощь</a>
    </div>
  </div>

  <div class="center">
    <div class="slider centerSlider">
      <div class="container jcarousel">
        <ul>
          <li>
            <div class="slide" style="background: url(<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam11.png);">
              <div class="inner">
                <div class="textHeader">
                  <h3>
                    Диски для обработки<br>дерева болгаркой
                  </h3>
                </div>
                <a href="/ru/shop/category/id/1369/" class="link">перейти</a>
              </div>
              <div class="caption">
                <div class="image">
                  <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam1min.jpg" />
                </div>                            
                <div class="text">
                  Используются на угловых шлифмашинах. Благодаря
                  конической форме обеспечивают удобство работы даже
                  на больших плоских поверхностях. Они предназначены
                  в основном для грубой шлифовки бревен и скругления
                  кромок дерева, пластика, резины и газобетона.
                </div>
              </div>
            </div>
          </li>
          <li>
            <div class="slide" style="background: url(<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam11.png);">
              <div class="inner">
                <div class="textHeader">
                  <h3>
                    Диски для обработки<br>дерева болгаркой
                  </h3>
                </div>
                <a href="/ru/shop/category/id/1369/" class="link">перейти</a>
              </div>
              <div class="caption">
                <div class="image">
                  <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam1min.jpg" />
                </div>                            
                <div class="text">
                  Используются на угловых шлифмашинах. Благодаря
                  конической форме обеспечивают удобство работы даже
                  на больших плоских поверхностях. Они предназначены
                  в основном для грубой шлифовки бревен и скругления
                  кромок дерева, пластика, резины и газобетона.
                </div>
              </div>
            </div>
          </li>
          <li>
            <div class="slide" style="background: url(<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam11.png);">
              <div class="inner">
                <div class="textHeader">
                  <h3>
                    Диски для обработки<br>дерева болгаркой
                  </h3>
                </div>
                <a href="/ru/shop/category/id/1369/" class="link">перейти</a>
              </div>
              <div class="caption">
                <div class="image">
                  <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/sam1min.jpg" />
                </div>                            
                <div class="text">
                  Используются на угловых шлифмашинах. Благодаря
                  конической форме обеспечивают удобство работы даже
                  на больших плоских поверхностях. Они предназначены
                  в основном для грубой шлифовки бревен и скругления
                  кромок дерева, пластика, резины и газобетона.
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div class="navControl jcarousel-pagination">
      </div>
    </div>
  </div>        

  <div class="right">
    <div class="slider rightSlider">
      <div class="container jcarousel">
        <ul>
          <?php if($core->lib->widget("ProductList")): ?>
<?php $core->widgets->ProductList()->setting("type_name", "list"); ?>
<?php $core->widgets->ProductList()->setting("param_id", "1"); ?>
<?php $core->widgets->ProductList()->setting("limit", "5"); ?>

<?php $core->widgets->ProductList()->appendSettings(); $core->widgets->ProductList()->main(); endif; ?>
          

          <?php if(count($this->vars['ProductListOut'])) : 
							   		 $this->vars['foreach']['ProductListOut']['count'] = count($this->vars['ProductListOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductListOut']);
							   		 foreach($this->vars['ProductListOut'] as $this->vars['foreach']['ProductListOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductListOut']['key']; ?>
          
          <li>
            <div class="slide">
			  <div class="inner">
                <div class="price-stiker">от <?php if(isset($this->vars['item']['modification_first_price'])) echo $this->vars['item']['modification_first_price']; ?> / шт</div>
                <div class="textHeader">
                  <img alt="<?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?>" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" />
                  <h3><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></h3>
                </div>
                <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>" class="link">перейти</a>
              </div>
            </div>
          </li>  
          <?php endforeach; ?><?php endif; ?>
        </ul>
      </div>
      <div class="navControl jcarousel-pagination">
      </div>
    </div>
  </div>
</div>

<div class="mainCats">
  <div class="mainCatsCategories">
    <div class="controls">
      <a href="#" class="prev">&nbsp;</a>
      <a href="#" class="next">&nbsp;</a>
    </div>
   
    <div class="container jcarousel catSlider">
      <ul class="catList" id="mainCatCarouselHeader">
        <?php if(count($this->vars['feeds'])) : 
							   		 $this->vars['foreach']['feeds']['count'] = count($this->vars['feeds']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feeds']);
							   		 foreach($this->vars['feeds'] as $this->vars['foreach']['feeds']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feeds']['key']; ?>
        	<li><a href="javascript:void(0)" class="<?php if(isset($this->vars['item']['icon'])) echo $this->vars['item']['icon']; ?>" item-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
        <?php endforeach; ?><?php endif; ?>
      </ul>
    </div>
  </div>        
  <div class="mainCatsGoods">
    <div id="feddsAllProducts" style="display:none">
      <?php if(count($this->vars['feeds'])) : 
							   		 $this->vars['foreach']['feeds']['count'] = count($this->vars['feeds']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feeds']);
							   		 foreach($this->vars['feeds'] as $this->vars['foreach']['feeds']['key'] => $this->vars['feedItem']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feeds']['key']; ?>
      <?php $this->vars['feedId'] = $this->vars['feedItem']['id']; ?>
      <ul class="goodsList" item-id="<?php if(isset($this->vars['feedId'])) echo $this->vars['feedId']; ?>">
      	<?php if(count($this->vars['feedsProduct'][$this->vars['feedId']])) : 
							   		 $this->vars['foreach']['feedsProduct.$feedId']['count'] = count($this->vars['feedsProduct'][$this->vars['feedId']]);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feedsProduct'][$this->vars['feedId']]);
							   		 foreach($this->vars['feedsProduct'][$this->vars['feedId']] as $this->vars['foreach']['feedsProduct_$feedId']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feedsProduct_$feedId']['key']; ?>
          <li>
            <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
              <div class="itemimage"><img alt="" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" /></div>
              <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
              <span class="price">
                <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
                	<?php if(isset($this->vars['item']['mod']['price'])) echo $this->vars['item']['mod']['price']; ?><i>/ за <?php if(isset($this->vars['item']['mod']['pack_size'])) echo $this->vars['item']['mod']['pack_size']; ?> <?php if(isset($this->vars['item']['mod']['pack_unit_name'])) echo $this->vars['item']['mod']['pack_unit_name']; ?></i>
                <?php else: ?>
                	<?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><i>/ за <?php if(isset($this->vars['item']['pack_size'])) echo $this->vars['item']['pack_size']; ?> <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></i>
                <?php endif; ?>
              </span>
            </a>
          </li>    
        <?php endforeach; ?><?php endif; ?>
      </ul>
      <?php endforeach; ?><?php endif; ?>
    </div>
    <div class="controls">
      <a href="#" class="prev">&nbsp;</a>
      <a href="#" class="next">&nbsp;</a>
    </div>
    <div class="container jcarousel itemsSlider" id="mainCatCarousel"></div>
  </div>
</div>


<div class="mainManufacturers contentContainer">
  <?php if($core->lib->widget("Vendors")): ?>
<?php $core->widgets->Vendors()->setting("limit", "5"); ?>
<?php $core->widgets->Vendors()->setting("random", "1"); ?>

<?php $core->widgets->Vendors()->appendSettings(); $core->widgets->Vendors()->main(); endif; ?>
  
  <?php if (isset($this->vars['VendorsOut']) && $this->vars['VendorsOut']  ): ?>
  <!--
  <p>
	Компания «Русконект» является одним из ведущих игроков на рынке оптовых поставок высококачественного 
	<b>крепежа</b> и сопутствующих товаров от ведущих отечественных и зарубежных производителей. Всегда в наличии – <b>нержавеющий крепеж оптом</b> в широчайшем ассортименте и по самым выгодным ценам.
</p>
<p>
	 Мы давно работаем на рынке оптовых поставок 
	<b>крепежных</b> изделий, обеспечивая своим заказчикам и постоянным партнерам бесперебойные поставки всех видов <b>крепежа</b> от гвоздей до такелажных цепей и прочих массивных грузовых и <b>строительных креплений</b>. Нашими клиентами являются предприятия всех форм собственности и различных размеров, которые осуществляют свою деятельность в области промышленного производства, строительства, отделки и ремонта, изготовления мебели, эксплуатации коммуникационных и коммунальных линий, розничной торговли строительными материалами и мн. др.
</p>
-->
  <h2>Производители и бренды</h2>
  
  <ul class="manList">     
    <?php if(count($this->vars['VendorsOut'])) : 
							   		 $this->vars['foreach']['VendorsOut']['count'] = count($this->vars['VendorsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['VendorsOut']);
							   		 foreach($this->vars['VendorsOut'] as $this->vars['foreach']['VendorsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['VendorsOut']['key']; ?>
    <li>
      <a href="/ru/shop/vendor/id/<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>/">
        <div class="manImage">
          <?php if (isset($this->vars['item']['text_image2']) && $this->vars['item']['text_image2']  ): ?><img alt="" src="<?php if(isset($this->vars['item']['text_image2'])) echo $this->vars['item']['text_image2']; ?>" /><?php endif; ?>
          <span class="caption"><?php if(isset($this->vars['item']['sub_description'])) echo $this->vars['item']['sub_description']; ?>	</span>
        </div>
        <div class="manLogo">
          <?php if (isset($this->vars['item']['image_original']) && $this->vars['item']['image_original']  ): ?><img alt="" src="<?php if(isset($this->vars['item']['image_original'])) echo $this->vars['item']['image_original']; ?>" /><?php endif; ?>
        </div>
      </a>
    </li>            
    <?php endforeach; ?><?php endif; ?>
  </ul>
  <?php endif; ?>
</div>
<div class="catalogIndex">
  <h2>Продукция</h2>
  <div class="preview">
    
    
  	<?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "31"); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
    <ul class="visible">
      <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']; ?>
      <li><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>" data-category="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
      <?php endforeach; ?><?php endif; ?>
    </ul>  
  </div>
  <div class="btn">
    <a href="javascript:void(0)" onclick="$(this).closest('.catalogIndex').find('.preview').animate({'height':'150px'}, 500)">Показать все категории</a>
  </div>

  
</div>

<div class="mainManufacturers contentContainer" style="display:none">
  <!--
  <p>
	 В нашем лице вы сможете найти исключительно надежного поставщика, который в состоянии закрыть любые потребности организаций на 
	<b>крепежные изделия в Москве</b> вне зависимости от эксклюзивности этих потребностей и необходимых объемов поставок. Постоянно в наличии – тысячи товарных позиций, причем ассортиментный перечень увеличивается буквально изо дня в день, а складские запасы растут.
</p>
<p>
	<h3>Преимущества поставок строительного крепежа от компании «Русконект»</h3>
<p>
	 Говоря о преимуществах нашей компании, мы сознательно не будем прибегать к громогласным и очень красивым по форме, но пустым по содержанию рекламным слоганам и «кричалкам». Компании, не первый год сотрудничающие с нами и регулярно покупающие
	<b> нержавеющий</b> <b>строительный крепеж оптом</b>, прекрасно знают о том, что нам не свойственно выпячивать себя и тем более пускать пыль в глаза. Поэтому мы ограничимся лишь беспристрастными достоверными фактами, которые помогут вам убедиться в том, что мы предлагаем действительно качественный <b>высокопрочный крепеж</b> и оказываем услуги самого высокого уровня:
</p>
<p>
	 · Мы работаем на рынке обеспечения поставок 
	<b>крепежа оптом в Москве</b> и Санкт-Петербурге уже много лет. Все эти годы мы напряженно трудились и активно развивались, налаживая новые прямые поставки, увеличивая ассортимент и складские запасы, повышая качество услуг и завоевывая все большую долю рынка, а также признание со стороны заказчиков и безупречную деловую репутацию. Мы по праву гордимся тем, что многие крупные компании уже не один год доверяют обеспечение их потребностей в <b>высокопрочном крепеже</b> компании «Русконект», выбирая нас генеральными поставщиками и постоянными партнерами.
</p>
<p>
	 · Мы поставляем практически любой 
	<b>крепеж</b>, востребованный на отечественном рынке. <b>Крепеж мелкий</b>, <b>строительный крепеж</b>, <b>грузовой крепеж</b>, приспособления для выполнения такелажных работ, петли, изделия для выполнения электромонтажных работ. Это лишь малая толика нашего <b>крепежного</b> ассортимента.
</p>
<p>
	 · Прямые поставки 
	<b>высокопрочного крепежа</b> от производителей и огромные товарные обороты позволяют нам формировать самую привлекательную стоимость предлагаемой продукции и предоставлять наиболее активным покупателям дополнительные скидки и особые условия обслуживания (индивидуальный менеджер, эксклюзивные предложения по оплате и отгрузке товара, товарная поддержка и т.д.);
</p>
<p>
	 · Помимо действительно огромного прайса на 
	<b>нержавеющий крепеж оптом</b>, мы предлагаем не менее внушительный список сопутствующих товаров. Это ящики для инструментов, шнуры, шпагаты и веревки, мебельные амортизаторы, стеллажи и прочие элементы для организации экспозиции;
</p>
<p>
	 · Также стоит отметить, что мы продаем 
	<b>крепежные изделия</b> не только крупным, но и мелким оптом. Следовательно, нашими постоянными и безмерно уважаемыми клиентами могут быть и молодые компании, и организации, бизнес которых не требует очень больших поставок
</p>
<p>
	 · Весь строительный 
	<b>высокопрочный крепёж</b> <b>оптом</b>, предлагаемый компанией «Русконект» имеет высочайшее качество, что подтверждено всей необходимой документацией.
</p>
<p>
	 Разумеется, это далеко не полный перечень аргументов в пользу того, чтобы покупать 
	<b>строительный крепеж оптом</b> в нашей организации. Но мы думаем, что и этих пунктов будет вполне достаточно, чтобы понять: мы – надежный поставщик, проверенный временем и имеющий действительно безупречную репутацию как среди покупателей, так и в профессиональной бизнес-среде.
</p>

  <br>
	<h2>Разнообразие применения высокопрочного крепежа от компании «Русконект»</h2>

<p>
	 Как уже говорилось, в ассортименте нашего предприятия можно найти самые различные 
	<b>крепежные изделия</b> и сопутствующие товары. Наши <b>крепежи</b> применяются, прежде всего, на разных этапах строительства, ремонта и отделки. Но совсем не ограничиваются только этим – товарный перечень «Русконект» значительно шире, чем только <b>строительный крепеж</b>. Остановимся на этом немного подробнее.
</p>
<p>
	<b>Грузовой крепеж</b> (грузоподъемные цепи, вантовые крепления, скобы, звенья, карабины, крюки и много другое) позволяют выполнять самые различные, в том числе и очень сложные погрузочно-разгрузочные и такелажные работы. А качество нашего <b>надежного крепежа</b> позволяет быть уверенным в надежности всей такелажной системы и безопасности при проведении данного типа работ.
</p>
<p>
	<b>Мелкий крепеж</b> имеет самое богатое ассортиментное разнообразие и применяется при выполнении огромного количества работ – как сугубо профессиональных, так и бытовых. Поэтому именно такие <b>крепежи</b> особенно активно покупаются не только строительными организациями, но предприятиями торговли, предлагающими крепежные изделия конечным пользователям и небольшим подрядным организациям и бригадам.
</p>
<p>
	 Скобяные изделия, представленные в нашем каталоге – это также 
	<b>высокопрочный крепёж</b>, представляющий собой все виды петель, задвижек, щеколды и ручек. Отличное качество, бесшумность, длительность эксплуатации – все это обеспечивают <b>крепежи</b> от предприятия «Русконект»! <b>Весь крепеж</b> безупречно надежен и долговечен – это подтверждено годами его продажи и использования на самых различных объектах, в том числе и в высоконагруженных конструкциях. Наши <b>крепежи</b> справляются с любыми задачами исключительно на «отлично»!
</p>
<p>
	 Постройки, сооружения и конструкции, в которых используется 
	<b>строительные крепления</b> «Русконект» производства ведущих компаний отрасли, отлично служат годами и десятилетиями, обеспечивая герметичность соединения, надежность и устойчивость конструкции. Покупайте <b>строительный крепеж оптом</b> у нас – и качество вашего строительства будет по-настоящему безупречным!
</p>
<p>
	<b>Где купить строительные крепления и нержавеющий крепеж оптом в Москве?</b>
</p>
<p>
	 Компаний, которые предлагают 
	<b>нержавеющий крепеж в Москве оптом</b>, существует множество. Но лишь немногие из них могут обеспечивать поставки любых объемов в кратчайшие сроки, иметь тысячи и тысячи товарных позиция в наличии и предоставлять самые выгодные условия сотрудничества. И «Русконект» - как раз одна из таких компаний.
</p>
<p>
	 Мы никогда не ставим во главу угла получение сиюминутной выгоды. Наша стратегическая задача – налаживание тесных партнерских отношений с потребителями 
	<b>строительных креплений</b> и любых других <b>крепежей</b> и долгосрочное сотрудничество на долгосрочной основе.
</p>
<p>
	 Поэтому, если вам необходим качественный и недорогой 
	<b>крепёж оптом</b> и надежный поставщик, всегда и в полном объеме выполняющий свои обязательства, то мы будем рады сотрудничеству с вами, предлагая самые лучшие условия для поставок с<b>троительного крепления</b> и прочих изделий!
</p>
<p>
	 По всем вопросам, касающимся поставок 
	<b>крепежей оптом</b> и мелким оптом, обязательно звоните нам! Компетентные и всегда исключительно доброжелательные сотрудники нашего офиса с радостью ответят на все ваши вопросы, предоставят любую дополнительную информацию про <b>крепеж потом</b> и расскажут еще больше о преимущества сотрудничества именно с нашей организацией. Мы уверены, что наше знакомство обязательно перерастет в постоянное сотрудничество и взаимовыгодное партнерство.
</p>
<p>
	 Будем рады видеть вашу компанию в числе наших заказчиков. Качественные 
	<b>крепежные изделия</b> оптом – только в компании «Русконект»! Мы – за <b>высококачественный крепеж</b>!
</p>-->
</div>