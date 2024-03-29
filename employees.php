<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/config/config.php");
  require_once($_CONFIG['AUTHORIZATION']['IS_LOGGED']);

  if (!$logged){
    header("HTTP/1.1 401 Unauthorized");
    include("401.php");
    exit;
  }
  else{
    setcookie("ref", $_SERVER['REQUEST_URI']);
    $page_title = "Сотрудники";
    $nav_active = 5;
    $fa = true;

    require_once($_CONFIG['EMPLOYEES']['INIT']);
?>

  <main role="main" id="main">
    <div class="container mb-5">
      <?if ($db):?>
        <!--Модальное окно удаления сотрудника-->
        <?if (isset($result)) :?>
          <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalLabel">Подтверждение удаления</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Вы точно хотите удалить сотрудника с именем <?=$name?>?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                  <a title="Удалить" href="<?=$_CONFIG['EMPLOYEES']['DEL']?>?id=<?=$id.$search_get?>" class="btn btn-primary" rel="nofollow">Удалить</a>
                </div>
              </div>
            </div>
          </div>
        <?endif;?>
        <!--Модальное окно сотрудника-->
        <div class="modal fade" id="handlerModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"><?=$id?"Редактировать сотрудника":"Добавить сотрудника"?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  
                  <form action="<?=$_CONFIG['EMPLOYEES']['HANDLE']?>" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                    <?if ($id):?>
                      <input type="hidden" id="id" name="id" value="<?=$id?>">
                    <?endif;?>
                    <?if ($search):?>
                      <input type="hidden" id="search" name="search" value="<?=$search?>">
                    <?endif;?>
                    <?if ($image_name):?>
                      <input type="hidden" id="image_name" name="image_name" value="<?=$image_name?>">
                    <?endif;?>
                    <div class="modal-body mb-2">
                      <div class="form-group">
                          <label for="name">ФИО</label>
                          <input type="text" class="form-control" id="name" name="name"  minlength=3 maxlength="100" value="<?=$name?>"  pattern="^[A-яёЁA-z\s]{3,100}$" required >
                          <div class="invalid-feedback">
                            Вы должны ввести ФИО сотрудника, максимум 100 символов.
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="tel">Телефон</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">+7</div>
                            </div>
                            <input type="tel" class="form-control" id="tel" name="tel" pattern="^\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}$" value="<?=$tel?>" required>
                          </div>
                          <div class="invalid-feedback">
                            Телефон введен неверно
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="post">Должность</label>
                        <input type="text" class="form-control" id="post" name="post" pattern="^[A-яёЁ\s]{3,30}$" value="<?=$post?>" minlength="3" maxlength="30" required>
                        <div class="invalid-feedback">
                          Введите должность русскими буквами
                        </div>
                      </div>
                      
                      <div class="d-flex justify-content-between align-items-end">
                        <?if($image_name):?>
                          <div id="photo_preview" class="mt-2" style="width=auto; margin-right:1rem">
                            <img id="photo" width="<?=$_CONFIG['EMPLOYEES']["IMAGE_W"]?>" height="<?=$_CONFIG['EMPLOYEES']["IMAGE_H"]?>" src="<?=$_CONFIG['EMPLOYEES']["PATH_TO_PHOTOS"].$image_name?>" alt="Preview">
                          </div>
                        <?else:?>
                          <div id="photo_preview" class="mt-2" style="width=0% ">
                          </div>
                        <?endif;?>
                        <div class="custom-file mb-0 h-100">
                          <label for="image">Фото сотрудника</label> 
                          <input type="file" class="custom-file-input" id="image" name="image" accept="image/jpg,image/png" <?=$id?"":"required"?>>
                          <label class="custom-file-label mb-0" for="image" style="margin-top: 30px; text-overflow: ellipsis; overflow: hidden; padding-right: 87СИ/px;"><?=$image_name ? $image_name : "Выберите файл" ?></label>
                          <div class="invalid-feedback">
                            Выберите файл .jpg или .png
                          </div>
                        </div> 
                      </div>    
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$id?"Отменить":"Закрыть"?></button>
                      <button type="submit" class="btn btn-primary"><?=$id?"Сохранить":"Добавить"?></button>
                    </div>
                  </form>
              </div>
          </div>
        </div>
        <div class="mb-3 d-flex justify-content-between">
          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#handlerModal"><i class="fas fa-user mr-2"></i>Добавить сотрудника</button>
          <div>
            <?if($search):?>
              <a href="employees" class="btn btn-sm btn-outline-secondary mr-2">Сбросить</a>
            <?endif;?>
            <form class="form-inline d-inline" id="search-form" action="" method="GET" novalidate>
              <input class="form-control form-control-sm mr-2" type="search" name="search" id="search" placeholder="Поиск сотрудника" aria-label="Поиск" value="<?=$search?>" <? if(!$search) echo("required")?>>
              <button class="btn btn-sm btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>
            
        <table class="w-100 table employees-table">
          <thead>
            <tr>
                <th>ФИО</th>
                <th class="text-center">Фотография</th>
                <th class="text-center">Телефон</th>
                <th class="text-center">Должность</th>
                <th class="text-center">Действия</th>
            </tr>
          </thead>
          <tbody>
              <?if ($search){
                echo($search_query);
                $str = $db->prepare("SELECT * FROM employees WHERE
                MATCH(`e_name`, `e_post`, `e_tel`)
                AGAINST('$search_query' IN BOOLEAN MODE)");
              }else{
                $str = $db->prepare("SELECT * FROM employees");
              }
              if ($str->execute()):
                $result = $str->fetchAll();
                if (count($result)>0):
                  foreach($result as $key => $result_value):?>
                    <tr>
                      <td><?=$result_value['e_name']?> </td>
                      <td class="text-center">
                        <img width="<?=$_CONFIG['EMPLOYEES']['IMAGE_W']?>" height="<?=$_CONFIG['EMPLOYEES']['IMAGE_H']?>" src="<?=$_CONFIG['EMPLOYEES']['PATH_TO_PHOTOS'].$result_value['e_photo']?>">
                      </td>
                      <td class="text-center"><?=format_phone($result_value['e_tel'])?></td>
                      <td class="text-center"><?=$result_value['e_post']?></td>
                      <td class="text-center">
                        <a title="Редактировать" href="employees?id=<?=$result_value['e_id']?>&state=1<?=$search_get?>" class="text-muted mr-2" rel="nofollow"><i class="fas fa-edit"></i></a>
                        <a title="Дублировать" href="<?=$_CONFIG['EMPLOYEES']['CPY']?>?id=<?=$result_value['e_id'].$search_get?>" class="text-muted mr-2" rel="nofollow"><i class="fas fa-copy"></i></a>
                        <a title="Удалить" href="employees?id=<?=$result_value['e_id']?>&state=2<?=$search_get?>" class="text-danger" rel="nofollow"><i class="fas fa-trash-alt"></i></a>
                      </td>
                    </tr>
                  <?endforeach;
                else:?>
                  </tbody>
                  </table>
                  <h4 class="text-center mt-5">
                    Не найдено ни одной записи!<br>
                    <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                  </h4>
                <?endif;
              else:?>
                </tbody>
                </table>
                <h4 class="text-center mt-5">
                  SQL запрос не был выполнен!<br>
                  <i class="fas fa-dizzy mt-5" style="font-size:256px"></i>
                </h4>
              <?endif;?>
          </tbody>
        </table>
      <?endif;?>
    </div>
</main>

<?
require_once($_CONFIG['TEMPLATES']['FOOTER_CRUD']);
}
?>