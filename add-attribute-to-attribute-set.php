<?php
	include 'app/Mage.php';
	Mage::app();
 
	if (isset($_POST['attribute_code'])) {
		$attributeCode = trim($_POST['attribute_code']);
		if (isset($_POST['attribute_group_name'])) {
			$attributeGroupName = trim($_POST['attribute_group_name']);
		}
		
		$entityType = Mage::getModel('eav/entity_type')->getCollection()->addFieldToFilter('entity_type_code', 'catalog_product')->getFirstItem();
		$attributeSetCollection = Mage::getModel('eav/entity_type')->load($entityType->getId())->getAttributeSetCollection();
		$attribute = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($attributeCode)->getFirstItem();
		$attributeGroupModel = Mage::getModel('eav/entity_attribute_group');
		
		foreach ($attributeSetCollection as $attributeSet) {
			if ($attributeGroupName) {
				$attributeGroup = $attributeGroupModel->getCollection()
					->addFieldToFilter('attribute_set_id', $attributeSet->getId())
					->addFieldToFilter('attribute_group_name', $attributeGroupName)
					->getFirstItem();
				
				if (!$attributeGroup->getId()) {
					$attributeGroup = $attributeGroupModel->setAttributeGroupName($attributeGroupName)
						->setAttributeSetId($attributeSet->getId())
						->save();
				}				
			} else {
				$attributeGroup = $attributeGroupModel->getCollection()
					->addFieldToFilter('attribute_set_id', $attributeSet->getId())
					->setOrder('attribute_group_id',ASC)
					->getFirstItem();
			}
			
			$attribute->setAttributeSetId($attributeSet->getId())
				->setAttributeGroupId($attributeGroup->getId())
				->setAttributeId($attribute->getId())
				->save();
			
			showLog("{$attribute->getAttributeCode()} has added to {$attributeSet->getAttributeSetName()} in {$attributeGroup->getAttributeGroupName()} group.");
        }
	}
	
	function showLog($message) {
		echo "<strong>{$message}</strong><br>";
	}
?>

<form method="post">
    <label for="attribute_code">Attribute Code:</label><br>
    <input name="attribute_code" id="attribute_code"><br>

    <label for="attribute_group_name">Attribute Set Group:</label><br>
    <input name="attribute_group_name" id="attribute_group_name"><br>

    <button>Save</button>
</form>
