<?php
namespace TDS\Model;


class ManyToMany extends Entity {

    /**
     * Undocumented function
     *
     * @param string $name
     * @param Entity $e1
     * @param Entity $e2
     * @param array $options
     * 
     * liste des options possibles :
     * - toutes les options pour la création d'une entité sont possibles
     * - 'opt1' : un tableau avec les options pour le lien vers l'entité 1
     * - 'opt2' : un tableau avec les options pour le lien vers l'entité 2
     * 
     */
    function __construct(string $name, Entity $e1, Entity $e2, array  $options = []) {
        parent::__construct($name, $options);
        $this->e1 = $e1;
        $this->e2 = $e2;

        $this->link1 = $this->addOneToMany($e1, $options['opt1'] ?? [], TRUE );
        $this->link2 = $this->addOneToMany($e2, $options['opt2'] ?? [], FALSE );
    }

    public function buildForModel($namespace){
        $app = \TDS\App::get();
        $modelName = $this->getModelName();
        $appName = $app::$appName;
        $extends = $namespace === $this->namespace ? 'ManyToMany' : '\\'.$this->namespace.'\\Model\\'.$this->name;
        return "<?php
namespace {$appName}\\Model;

use \\TDS\\ManyToMany;
use \\TDS\\App;

class {$this->name} extends {$extends} implements \\Model\\{$modelName}interface_ {
    use \\Model\\{$modelName};

    const __LEFT__ = \"{$this->link1->getMappedBy()}\";
    const __RIGHT__ = \"{$this->link2->getMappedBy()}\";
}        
        ";
    }

    function getDataToBuildModel(Entity $sourceEntity){
        $targetEntity = $sourceEntity == $this->e1 ? $this->e2 : $this->e1;
        $joinTable = '\\'.Model::$appName.'\\Model\\'.$this->name;
        return ['\\'.Model::$appName.'\\Model\\'.$targetEntity->getName(), $joinTable];        
    }
/*
    public function bmtmftmt($mn, $n, $u, $tn){
        $this-> buildManyToManyForTargetModelTwig($mn, $n, $u, $tn);
    }
*/

    function buildManyToManyForTargetModelTwig(string $mainName, string $name, Entity $up, bool $isFirst, $twigName){
        // il faudrat peut-être compliqué le truc ici pour prendre en compte le fait que
        // l'on peut avoir des entitées chainées les unes aux autres.. (mais en fait c'est plutôt dans le oneToMany)
        
//        $toLink =  ($up->getName()  == $this->e1->getName()) ? $this->link2 : $this->link1;
//        $fromLink =  ($up->getName()  == $this->e1->getName()) ? $this->link1 : $this->link2;
        
        $fromLink =  $isFirst ? $this->link1 : $this->link2;
        $toLink =  $isFirst ? $this->link2 : $this->link1;

        $fieldList = \array_slice($this->fieldList,2+($this->withActif?1:0)); // C'était 2 ! (avant qu'on ait actif ?)
        $a = "
        <table class='w3-table-all w3-hoverable w3-small'>
            <thead>
                {% block header_tr_{$twigName} %}
                <tr class='w3-light-blue'>
                    <th class=\"w3-left\">{% if not isCreation %}<a href='/{{ App.appName }}/CRUD/{$this->name}/{$fromLink->getMappedBy()}/{{ {$mainName}.id }} '><i class='fa fa-plus' aria-hidden='true'></i></a>{% endif %}</th>
                    
                    {% block header_th_{$twigName}_{$toLink->getName()} %}
                    <th class='w3-center'>{$toLink->getName()}</th>
                    {% endblock header_th_{$twigName}_{$toLink->getName()} %}";
            foreach($fieldList as $field){
                $a.="
                    {% block header_th_{$twigName}_{$field->getName()} %}
                    <th class='w3-center'>{$field->getName()}</th>
                    {% endblock header_th_{$twigName}_{$field->getName()} %}";
            }
        $a.="       
                    {% block header_end{$twigName} %}
                    {% endblock header_end{$twigName} %}
                    <th class=\"w3-right\" ></th>
                </tr>
                {% endblock header_tr_{$twigName} %}
            </thead>

            <tbody>
            {% for {$this->name}  in {$mainName}.{$name} %}
                {% block line_{$twigName} %}
                <tr name='{$name}' _id_='{{ {$this->name}.id }}' class='{{ {$this->name}.actif?\"\":\"passif \" }}{{ {$this->name}.{$toLink->getMappedBy()}.actif?\"\":\"passifLink \" }}'>
                    <td >
                    <i class='fas fa-history history_ManyToMany' data-tipped-options='ajax: {data: { E: \"{$this->name}\", id: \"{{ {$this->name}.id }}\" }}'></i>
                    {{ {$this->name}.getCrudEditLink('<i class=\"fas fa-edit\" ></i>')|raw }}</td>    
                    {% block line_{$this->name}_{$toLink->getTwigName()} %}
                    <th class='w3-left'>{{ {$this->name}.{$toLink->getMappedBy()}.getGeneric( true )|raw }}</th>
                    {% endblock line_{$this->name}_{$toLink->getTwigName()} %}";

            foreach($fieldList as $field){
                $a.="
                    {% block line_{$twigName}_{$field->getTwigName()} %}
                    <td class='w3-center'>{{ {$this->name}.{$field->getName()} }}</td>
                    {% endblock line_{$twigName}_{$field->getTwigName()} %}";
                }

            $a.="
                    {% block line_end{$twigName} %}
                    {% endblock line_end{$twigName} %}
                    <td><a onclick='doDeleteLink(this)'><i class=\"fas fa-trash\" ></i></a></td>
                </tr>
                {% endblock  line_{$twigName} %}
            {% endfor %}
            {% block footer_end{$twigName} %}
            {% endblock footer_end{$twigName} %}
            </tbody>
        </table>";
        return $a;
    }
}
