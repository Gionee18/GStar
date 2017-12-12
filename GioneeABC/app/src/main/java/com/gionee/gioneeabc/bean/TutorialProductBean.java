package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by root on 25/10/16.
 */

public class TutorialProductBean {
    public int id;
    public int category_id;
    public String product_name;
    public String product_desc;
    public String is_new;
    public String status;
    public String desc1;
    public String desc2;
    public String desc3;
    public int position;
    public String created_at;
    public String updated_at;
    public ArrayList<ProImage> pro_image;

    public Tutorials tutorials;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getCategory_id() {
        return category_id;
    }

    public void setCategory_id(int category_id) {
        this.category_id = category_id;
    }

    public String getProduct_name() {
        return product_name;
    }

    public void setProduct_name(String product_name) {
        this.product_name = product_name;
    }

    public String getProduct_desc() {
        return product_desc;
    }

    public void setProduct_desc(String product_desc) {
        this.product_desc = product_desc;
    }

    public String getIs_new() {
        return is_new;
    }

    public void setIs_new(String is_new) {
        this.is_new = is_new;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDesc1() {
        return desc1;
    }

    public void setDesc1(String desc1) {
        this.desc1 = desc1;
    }

    public String getDesc2() {
        return desc2;
    }

    public void setDesc2(String desc2) {
        this.desc2 = desc2;
    }

    public String getDesc3() {
        return desc3;
    }

    public void setDesc3(String desc3) {
        this.desc3 = desc3;
    }

    public int getPosition() {
        return position;
    }

    public void setPosition(int position) {
        this.position = position;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public String getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(String updated_at) {
        this.updated_at = updated_at;
    }

    public ArrayList<ProImage> getPro_image() {
        return pro_image;
    }

    public void setPro_image(ArrayList<ProImage> pro_image) {
        this.pro_image = pro_image;
    }

    public Tutorials getTutorials() {
        return tutorials;
    }

    public void setTutorials(Tutorials tutorials) {
        this.tutorials = tutorials;
    }

    private class ProImage {
        public int image_id;
        public String name;
        public String title;
        public String path;

    }

    private class Tutorials {
    }
}
