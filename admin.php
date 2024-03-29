<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");

  if (!$logged){
    header("HTTP/1.1 401 Unauthorized");
    include("401.php");
    exit;
  }
  else{
    $page_title = "Админ панель";
    $nav_active = 6;
    $fa = false;
    
    setcookie("ref", $_SERVER['REQUEST_URI']);
    require_once($_CONFIG['DATABASE']['CONNECT']);
    require_once($_CONFIG['TEMPLATES']['HEADER']);
?>

  <main role="main" id="main" style="min-height: 100vh" class="bg-dark">
      <div class="container">

          <?if ($db): 
              $pages = $db->query("SELECT * FROM pages");
              $visitors = $db->query("SELECT * FROM analytics");
          ?>
          <table class="table table-dark">
            <thead>
              <tr>
                <th scope="col">Страница</th>
                <th scope="col" class="text-center">Общее число посетителей</th>
                <th scope="col" class="text-center">Уникальные</th>
                <th scope="col" class="text-center">Переходили из</th>
                <th scope="col" class="text-center">Действия</th>
              </tr>
            </thead>
            <tbody>
              <? foreach($pages as $key => $page){
                  $page_id = $page["page_id"];?>
                  
                  <tr>
                      <th scope="row"><a href="<?=$page["page_url"]?>"><?=$page["page_title"]?></a></th>
                      <td class="text-center">
                      <?
                        $count = 0;
                        $str = $db->prepare("SELECT visited_this_page FROM analytics WHERE visited_page_id = $page_id");
                        $str->execute();
                        $result = $str->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result as $value) {
                          $count += (int)$value["visited_this_page"];
                        }
                        echo $count;
                      ?>
                      </td>
                      <td class="text-center">
                      <?
                        $str = $db->prepare("SELECT visitor_id FROM analytics WHERE visited_page_id = $page_id");
                        $str->execute();
                        $result = $str->fetchAll(PDO::FETCH_ASSOC);
                        echo count($result);
                      ?>
                      </td>
                      <td class="text-center" style="width: 25%; ">
                      <?
                        $str = $db->prepare("SELECT visitor_ref FROM analytics WHERE visited_page_id = $page_id");
                        $str->execute();
                        $result = $str->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result as $key => $value) {
                          if ($key == 0)
                            echo "<a href=\"".$value["visitor_ref"]."\">".$value["visitor_ref"]."</a>";
                          else
                            echo ", <a href=\"".$value["visitor_ref"]."\">".$value["visitor_ref"]."</a>";
                        }
                      ?>
                      </td>
                      <td class="text-center" style="width: 15%; ">
                        <a title="Удалить" href="<?=$_CONFIG['ANALITYCS']['DEL']?>?id=<?=$page_id?>" class="text-white" rel="nofollow"><i class="far fa-trash-alt"></i></a>
                      </td>
                  </tr>
              <?}?>
              
            </tbody>
          </table>
          <?endif;?>
      </div>
  </main> 
  <?
  require_once($_CONFIG['TEMPLATES']['FOOTER_BOOTSTRAP']);
}?>