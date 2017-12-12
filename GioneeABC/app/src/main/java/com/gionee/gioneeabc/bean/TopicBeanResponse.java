package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by admin on 21-12-2016.
 */
public class TopicBeanResponse {
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

    private Data data;

    public Data getData() {
        return this.data;
    }

    public void setData(Data data) {
        this.data = data;
    }

    public class Data {
        private ArrayList<UpdateResponseBean.Topic> topics;

        public ArrayList<UpdateResponseBean.Topic> getTopics() {
            return this.topics;
        }

        public void setTopics(ArrayList<UpdateResponseBean.Topic> topics) {
            this.topics = topics;
        }
    }
}
