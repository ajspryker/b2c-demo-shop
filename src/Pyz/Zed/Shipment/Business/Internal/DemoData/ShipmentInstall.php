<?php

namespace Pyz\Zed\Shipment\Business\Internal\DemoData;

use Generated\Shared\Transfer\ShipmentCarrierTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use SprykerFeature\Zed\Installer\Business\Model\AbstractInstaller;
use SprykerFeature\Zed\Shipment\Business\Model\Carrier;
use SprykerFeature\Zed\Shipment\Business\Model\Method;
use SprykerFeature\Zed\Shipment\Persistence\ShipmentQueryContainerInterface;

class ShipmentInstall extends AbstractInstaller
{

    /**
     * @var ShipmentQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var Method
     */
    protected $method;

    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @param ShipmentQueryContainerInterface $queryContainer
     * @param Carrier $carrier
     * @param Method $method
     */
    public function __construct(
        ShipmentQueryContainerInterface $queryContainer,
        Carrier $carrier,
        Method $method
    ) {
        $this->queryContainer = $queryContainer;
        $this->carrier = $carrier;
        $this->method = $method;
    }

    public function install()
    {
        $this->info('This will install a Carrier Company and Shipment Method in the demo shop');

        if ($this->queryContainer->queryMethods()->count() > 0) {
            $this->warning('Dummy Shipment data is already installed. Skipping.');

            return;
        }

        $idCarrier = $this->createCarrier();
        $this->addShipmentMethodToCarrie($idCarrier);
    }

    /**
     * @return int
     */
    protected function createCarrier()
    {
        $shipmentCarrierTransfer = new ShipmentCarrierTransfer();
        $shipmentCarrierTransfer->setName('International Parcel Service (IPS)');
        $shipmentCarrierTransfer->setGlossaryKeyName('shipment.international.parcel.service');
        $shipmentCarrierTransfer->setIsActive(true);

        return $this->carrier->create($shipmentCarrierTransfer);
    }

    /**
     * @param $idCarrier
     */
    protected function addShipmentMethodToCarrie($idCarrier)
    {
        $shipmentMethodTransfer = new ShipmentMethodTransfer();
        $shipmentMethodTransfer->setFkShipmentCarrier($idCarrier);
        $shipmentMethodTransfer->setName('Standart');
        $shipmentMethodTransfer->setGlossaryKeyName('shipment.standart.name');
        $shipmentMethodTransfer->setGlossaryKeyDescription('shipment.standart.description');
        $shipmentMethodTransfer->setPrice(490);
        $shipmentMethodTransfer->setIsActive(true);

        $this->method->create($shipmentMethodTransfer);

        $shipmentMethodTransfer = new ShipmentMethodTransfer();
        $shipmentMethodTransfer->setFkShipmentCarrier($idCarrier);
        $shipmentMethodTransfer->setName('Express');
        $shipmentMethodTransfer->setGlossaryKeyName('shipment.express.name');
        $shipmentMethodTransfer->setGlossaryKeyDescription('shipment.express.description');
        $shipmentMethodTransfer->setPrice(590);
        $shipmentMethodTransfer->setIsActive(true);

        $this->method->create($shipmentMethodTransfer);
    }

}