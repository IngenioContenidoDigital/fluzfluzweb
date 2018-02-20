<?php

class AdminPosslideshowController extends ModuleAdminController
{
	public function __construct()
	{
		$this->table = 'pos_slideshow';
		$this->className = 'Nivoslideshow';
		$this->lang = true;
		$this->bootstrap = true;
		$this->deleted = false;
		$this->colorOnBackground = false;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
		$this->context = Context::getContext();
                
                $this->fieldImageSettings = array(
                        'name' => 'image',
                        'dir' => 'blockslideshow'
 		);
                $this->imageType = "jpg";
		
		parent::__construct();
	}
        
        public function renderList() {
            
            $this->addRowAction('edit');
            $this->addRowAction('delete');
            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?')
                )
            );

            $this->fields_list = array(
                'id_pos_slideshow' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'width' => 25
                ),
                  'title' => array(
                    'title' => $this->l('Title'),
                    'width' => 90,
                ),
                'type_view' => array(
                    'title' => $this->l('Vista'),
                    'width' => 90,
                ),
                  'type_route' => array(
                    'title' => $this->l('Tipo ruta'),
                    'width' => 90,
                ),
                  'link' => array(
                    'title' => $this->l('Link Web'),
                    'width' => 90,
                ),
                'link_app' => array(
                    'title' => $this->l('Link App'),
                    'width' => 90,
                ),
                'description' => array(
                    'title' => $this->l('Desscription'),
                    'width' => '300',
                 ),
				 'active' => array(
					 'title' => $this->l('Displayed'), 
					 'width' => 25, 
					 'align' => 'center', 
					 'active' => 'active', 
					 'type' => 'bool', 
					 'orderby' => FALSE
					 ),
                  'porder' => array(
                    'title' => $this->l('Order'),
                    'width' => 10,
                ),
				
            );
            
           $this->fields_list['image'] = array(
                'title' => $this->l('Image'),
                'width' => 70,
                'image' => $this->fieldImageSettings["dir"]
                //'image_baseurl' => _S3_PATH_."home/3.jpg"
            );
//            

            $lists = parent::renderList();
            parent::initToolbar();

            return $lists;
    }
    
    
    public function renderForm() {
        
        $options_list[0] = array(id_value => 0, name_type => 'Id Fabricante');
        $options_list[1] = array(id_value => 1, name_type => 'Id Categoria');
        $options_list[2] = array(id_value => 2, name_type => 'Url');
        
        $type_view[0] = array(id_view => 0, name_view => 'Web');
        $type_view[1] = array(id_view => 1, name_view => 'Movil');
        $type_view[2] = array(id_view => 2, name_view => 'Web / Movil');
        
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Slideshow'),
                'image' => '../img/admin/cog.gif'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'size' => 40,
					'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Tipo de visualizacion'),
                    'name' => 'type_view',
                    'required' => true,
                    'desc' => $this->l('choose type from your computer.'),
                    'options' => array(
                        'query' => $type_view,
                        'id' => 'id_view',
                        'name' => 'name_view'
                    ),
                    'hint' => $this->l('Selecciona el tipo de visualizacion. web, movil o web')
                ), 
                array(
                    'type' => 'select',
                    'label' => $this->l('Tipo de enrutamiento'),
                    'name' => 'type_route',
                    'required' => true,
                    'desc' => $this->l('choose type from your computer.'),
                    'options' => array(
                        'query' => $options_list,
                        'id' => 'id_value',
                        'name' => 'name_type'
                    ),
                    'hint' => $this->l('Selecciona el tipo de enrutamiento. Producto, Categoria o Url')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link Web:'),
                    'name' => 'link',
                    'size' => 40,
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link App:'),
                    'name' => 'link_app',
                    'size' => 40,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image:'),
                    'name' => 'image',
                    'desc' => $this->l('Upload  a banner from your computer.')
                ),
              array(
                'type' => 'textarea',
                'label' => $this->l('Description'),
                'name' => 'description',
                'autoload_rte' => TRUE,
                'lang' => true,
                'required' => TRUE,
                'rows' => 5,
                'cols' => 40,
                'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
               ),
				 array(
                    'type' => 'radio',
                    'label' => $this->l('Displayed:'),
                    'name' => 'active',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => FALSE,
                    'values' => array(array(
                            'id' => 'require_on',
                            'value' => 1,
                            'label' => $this->l('Yes')), array(
                            'id' => 'require_off',
                            'value' => 0,
                            'label' => $this->l('No')))),
				array(
                    'type' => 'text',
                    'label' => $this->l('Order:'),
                    'name' => 'porder',
                    'size' => 40,
                    'require' => false
                ),
            ),
             'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
                 if (Shop::isFeatureActive())
                $this->fields_form['input'][] = array(
                        'type' => 'shop',
                        'label' => $this->l('Shop association:'),
                        'name' => 'checkBoxShopAsso',
                );

        if (!($obj = $this->loadObject(true)))
            return;


        return parent::renderForm();
    }
    
    public function processSave() {
        if ( Tools::isSubmit('submitAddpos_slideshow') ) {
            $awsObj = new Aws();
            if (!($awsObj->setObjectImage($_FILES['image']['tmp_name'],basename($_POST["id_pos_slideshow"].".jpg"),'home/'))) {
                $this->errors[] = Tools::displayError('No fue posible cargar la imagen.');
            }
        }
        return parent::processSave();
    }

}
