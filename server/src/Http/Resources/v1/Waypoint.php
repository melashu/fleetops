<?php

namespace Fleetbase\FleetOps\Http\Resources\v1;

use Fleetbase\FleetOps\Models\Waypoint as WaypointModel;
use Fleetbase\Http\Resources\FleetbaseResource;
use Fleetbase\Support\Http;
use Fleetbase\Support\Resolve;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class Waypoint extends FleetbaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $waypoint               = $this->getWaypoint();

        return [
            'id'                   => $this->when(Http::isInternalRequest(), $this->id, $this->public_id),
            'uuid'                 => $this->when(Http::isInternalRequest(), $this->uuid),
            'public_id'            => $this->when(Http::isInternalRequest(), $this->public_id),
            'waypoint_public_id'   => $this->when(Http::isInternalRequest(), $waypoint->public_id),
            'order'                => $waypoint->order,
            'tracking'             => $waypoint->tracking,
            'status'               => $waypoint->status,
            'status_code'          => $waypoint->status_code,
            'name'                 => $this->name,
            'location'             => data_get($this, 'location', new Point(0, 0)),
            'address'              => $this->address,
            'address_html'         => $this->when(Http::isInternalRequest(), $this->address_html),
            'street1'              => $this->street1 ?? null,
            'street2'              => $this->street2 ?? null,
            'city'                 => $this->city ?? null,
            'province'             => $this->province ?? null,
            'postal_code'          => $this->postal_code ?? null,
            'neighborhood'         => $this->neighborhood ?? null,
            'district'             => $this->district ?? null,
            'building'             => $this->building ?? null,
            'security_access_code' => $this->security_access_code ?? null,
            'country'              => $this->country ?? null,
            'country_name'         => $this->when(Http::isInternalRequest(), $this->country_name),
            'phone'                => $this->phone ?? null,
            'owner'                => $this->when(!Http::isInternalRequest(), Resolve::resourceForMorph($this->owner_type, $this->owner_uuid)),
            'tracking_number'      => $this->whenLoaded('trackingNumber', $waypoint->trackingNumber),
            'type'                 => $this->type,
            'meta'                 => data_get($this, 'meta', []),
            'updated_at'           => $this->updated_at,
            'created_at'           => $this->created_at,
        ];
    }

    /**
     * Finds the waypoint got a payload and place
     *
     * @return WaypointModel|null
     */
    private function getWaypoint(): ?WaypointModel
    {
        return WaypointModel::where(['payload_uuid' => $this->payload_uuid, 'place_uuid' => $this->uuid])->without(['place'])->with(['trackingNumber'])->first();
    }

    /**
     * Transform the resource into an webhook payload.
     *
     * @return array
     */
    public function toWebhookPayload()
    {
        return [
            'id'              => $this->public_id,
            'internal_id'     => $this->internal_id,
            'name'            => $this->name,
            'type'            => data_get($this, 'type'),
            'destination'     => $this->destination ? $this->destination->public_id : null,
            'customer'        => Resolve::resourceForMorph($this->customer_type, $this->customer_uuid),
            'tracking_number' => new TrackingNumber($this->trackingNumber),
            'description'     => data_get($this, 'description'),
            'photo_url'       => data_get($this, 'photo_url'),
            'length'          => data_get($this, 'length'),
            'width'           => data_get($this, 'width'),
            'height'          => data_get($this, 'height'),
            'dimensions_unit' => data_get($this, 'dimensions_unit'),
            'weight'          => data_get($this, 'weight'),
            'weight_unit'     => data_get($this, 'weight_unit'),
            'declared_value'  => data_get($this, 'declared_value'),
            'price'           => data_get($this, 'price'),
            'sale_price'      => data_get($this, 'sale_price'),
            'sku'             => data_get($this, 'sku'),
            'currency'        => data_get($this, 'currency'),
            'meta'            => $this->meta ?? [],
            'updated_at'      => $this->updated_at,
            'created_at'      => $this->created_at,
        ];
    }
}
