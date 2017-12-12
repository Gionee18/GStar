package com.gionee.gioneeabc.bean;

import java.io.Serializable;
import java.util.ArrayList;

/**
 * Created by admin on 30-11-2016.
 */
public class UpdateResponseBean {
    private int count;

    public int getCount() {
        return this.count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    private String status;

    public String getStatus() {
        return this.status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    private ArrayList<Category> data;

    public ArrayList<Category> getData() {
        return this.data;
    }

    public void setData(ArrayList<Category> data) {
        this.data = data;
    }

    public class Topic implements Serializable{
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private int category_id;

        public int getCategoryId() {
            return this.category_id;
        }

        public void setCategoryId(int category_id) {
            this.category_id = category_id;
        }

        private int subcategory_id;

        public int getSubcategoryId() {
            return this.subcategory_id;
        }

        public void setSubcategoryId(int subcategory_id) {
            this.subcategory_id = subcategory_id;
        }

        private String topic_name;

        public String getTopicName() {
            return this.topic_name;
        }

        public void setTopicName(String topic_name) {
            this.topic_name = topic_name;
        }

        private String status;

        public String getStatus() {
            return this.status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        private String topic_desc;

        public String getTopicDesc() {
            return this.topic_desc;
        }

        public void setTopicDesc(String topic_desc) {
            this.topic_desc = topic_desc;
        }

        private int is_read;

        public int getIsRead() {
            return this.is_read;
        }

        public void setIsRead(int is_read) {
            this.is_read = is_read;
        }

        public boolean isArchive;

        public boolean isArchive() {
            return isArchive;
        }

        public void setArchive(boolean archive) {
            isArchive = archive;
        }

    }

    public class Subcategory implements Serializable{
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String subcategory_name;

        public String getSubcategory_name() {
            return subcategory_name;
        }

        public void setSubcategory_name(String subcategory_name) {
            this.subcategory_name = subcategory_name;
        }

        private ArrayList<Topic> topic;

        public ArrayList<Topic> getTopic() {
            return this.topic;
        }

        public void setTopic(ArrayList<Topic> topic) {
            this.topic = topic;
        }
        public int getUnreadCount() {
            return unreadCount;
        }

        public void setUnreadCount(int unreadCount) {
            this.unreadCount = unreadCount;
        }

        public int unreadCount;
    }

    public class Category {
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String category_name;

        public String getCategoryName() {
            return this.category_name;
        }

        public void setCategoryName(String category_name) {
            this.category_name = category_name;
        }


        private ArrayList<Subcategory> subcategory;

        public ArrayList<Subcategory> getSubcategory() {
            return this.subcategory;
        }

        public void setSubcategory(ArrayList<Subcategory> subcategory) {
            this.subcategory = subcategory;
        }
        public int getUnreadCount() {
            return unreadCount;
        }

        public void setUnreadCount(int unreadCount) {
            this.unreadCount = unreadCount;
        }

        public int unreadCount;

    }


}
