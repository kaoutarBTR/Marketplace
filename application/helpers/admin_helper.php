<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 *	@author : AyoubKH
 *  @support: support@ayoubkh.com
 *	date	: 05 June, 2015
 *	S.A.S. BORSO
 *	http://www.ayoubkh.com
 *  version: 1.0
 */

function btn_edit($uri)
{
    return anchor($uri, '<i class="glyphicon glyphicon-edit"></i>', array('class' => "btn bg-navy btn-xs", 'title' => 'Modifier', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_edit_disable($uri)
{
    return anchor($uri, '<span class="glyphicon glyphicon-pencil"></span>', array('class' => "btn btn-primary btn-xs disabled", 'title' => 'Modifier', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_edit_modal($uri)
{
    return anchor($uri, '<span class="glyphicon glyphicon-pencil"></span>', array('class' => "btn btn-primary btn-xs", 'title' => 'Modifier', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-toggle' => 'modal', 'data-target' => '#myModal'));
}

function btn_view_modal($uri)
{
    return anchor($uri, '<span class="glyphicon glyphicon-search"></span>', array('class' => "btn bg-olive btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'View', 'data-toggle' => 'modal', 'data-target' => '#myModal'));
}

function btn_delete($uri, $type = '')
{

    if ($type == 'marque') {
        $msg = 'Êtes-vous sûr de vouloir supprimer cette marque ? Toutes les lignes, coloris et produits liées à cette marque seront supprimées';
    } elseif ($type == 'ligne') {
        $msg = 'Êtes-vous sûr de vouloir supprimer cette ligne ? Toutes les coloris et produits liées à cette ligne seront supprimées';
    } elseif ($type == 'coloris') {
        $msg = 'Êtes-vous sûr de vouloir supprimer cette coloris ? Tout les produits liés à cette coloris seront supprimés';
    } else {
        $msg = 'Êtes-vous sûr de vouloir supprimer cet enregistrement ?';
    }

    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Supprimer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('" . $msg . "');"
    ));
}

function btn_delete_confirmation($uri)
{
    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Supprimer', 'data-toggle' => 'tooltip', 'data-placement' => 'top'
    ));
}

function btn_delete_product($uri)
{
    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Supprimer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "confirm('Êtes vous sur de vouloir supprimer ce produit (Attention : action irréversible) ?');",
    ));
}


function btn_delete_disable($uri)
{
    return anchor($uri, '<i class="fa fa-trash-o"></i>', array(
        'class' => "btn btn-danger btn-xs disabled", 'title' => 'Supprimer', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
    ));
}

function btn_active($uri)
{
    return anchor($uri, '<i class="fa fa-check"></i>', array(
        'class' => "btn btn-success btn-xs", 'title' => 'Active', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to active new sesion . This cannot be undone. Are you sure?');"
    ));
}

function btn_print()
{
    return anchor('', '<span class="glyphicon glyphicon-print"></i></span>', array('class' => "btn btn-primary btn-xs", 'title' => 'Print', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => 'printDiv("printableArea")'));
}

function btn_atndc_print()
{
    return anchor('', '<span class="glyphicon glyphicon-print"></i></span>', array('class' => "btn btn-customs btn-xs", 'title' => 'Print', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => 'printDiv("printableArea")'));
}

function btn_pdf($uri)
{
    return anchor($uri, '<span <i class="fa fa-file-pdf-o"></i></span>', array('class' => "btn btn-primary btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Pdf'));
}

function btn_excel($uri)
{
    return anchor($uri, '<span <i class="fa fa-file-excel-o"></i></span>', array('class' => "btn btn-primary btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Excel'));
}

function btn_view($uri)
{
    return anchor($uri, '<span class="glyphicon glyphicon-search"></span>', array('class' => "btn bg-olive btn-xs", 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Voir'));
}

function btn_save($uri)
{
    return anchor($uri, '<span <i class="fa fa-plus-circle"></i></span>', array('class' => "btn btn-success btn-xs", 'title' => 'Save', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_add($uri)
{
    return anchor($uri, '<span <i class="fa fa-plus-square"></i></span>', array('class' => "btn btn-success btn-xs", 'title' => 'Add Routine', 'data-toggle' => 'tooltip', 'data-placement' => 'top'));
}

function btn_publish($uri)
{
    return anchor($uri, '<i class="fa fa-check"></i>', array(
        'class' => "btn btn-success btn-xs", 'title' => 'Click to Unpublish', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to unpublish an exam. Are you sure?');"
    ));
}

function btn_unpublish($uri)
{
    return anchor($uri, '<i class="fa fa-times"></i>', array(
        'class' => "btn btn-danger btn-xs", 'title' => 'Click to PUblish', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'onclick' => "return confirm('You are about to publish an exam. Are you sure?');"
    ));
}

function get_userName($id)
{
    $CI =& get_instance();
    $CI->db->where('user_id', $id);
    $query = $CI->db->get("tbl_user");
    if ($query->row())
        return $query->row()->name;
    else
        return '';
}

function get_client_type($id)
{
    $CI =& get_instance();
    $CI->db->where('customer_id', $id);
    $query = $CI->db->get("tbl_customer");
    if ($query->row())
        return $query->row()->type_client;
    else
        return '';
}

function get_frais_de_port_type($type)
{
    if($type == "franco") {
        return "Franco";
    } elseif($type == "port_du") {
        return "Port du";
    }

    return "";
}




