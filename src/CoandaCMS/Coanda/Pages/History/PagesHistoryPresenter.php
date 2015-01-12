<?php namespace CoandaCMS\Coanda\Pages\History;

class PagesHistoryPresenter {

    /**
     * @param $parameters
     * @return mixed
     */
    public function present($parameters)
    {
        if (method_exists($this, 'handle' . camel_case($parameters['action'])))
        {
            return $this->{'handle' . camel_case($parameters['action'])}($parameters['for_id'], $parameters['data']);
        }

        return $parameters['action'];
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getPage($id)
    {
        return \Coanda::pages()->getPage($id);
    }

    /**
     * @param $id
     * @return string
     */
    private function pageName($id)
    {
        $page = $this->getPage($id);

        if ($page)
        {
            return '<a href="' . \Coanda::adminUrl('pages/view/' . $page->id) . '">' . $page->name . '</a>';
        }

        return '<em>* page not found *</em> (#' . $id . ')';
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleInitialVersion($id, $data)
    {
        return 'Created initial version of ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handlePublishVersion($id, $data)
    {
        return 'Published version #' . $data['version'] . ' of ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleNewVersion($id, $data)
    {
        return 'Created version #' . $data['version'] . ' of ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleDiscardVersion($id, $data)
    {
        return 'Discarded version #' . $data['version'] . ' of ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleOrderChanged($id, $data)
    {
        return 'Order changed for ' . $this->pageName($id) . ' to ' . $data['new_order'];
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleRestored($id, $data)
    {
        return 'Restored ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleTrashed($id, $data)
    {
        return 'Trashed ' . $this->pageName($id);
    }

    /**
     * @param $id
     * @param $data
     * @return string
     */
    private function handleDeleted($id, $data)
    {
        return 'Deleted "' . (isset($data['page_name']) ? $data['page_name'] : '') . '" (#' . $id . ')';
    }
}
