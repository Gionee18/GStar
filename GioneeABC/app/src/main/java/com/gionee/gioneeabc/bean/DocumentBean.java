package com.gionee.gioneeabc.bean;

import java.io.Serializable;

/**
 * Created by Linchpin25 on 3/2/2016.
 */
public class DocumentBean implements Serializable{
    private int docId;
    private String docType;
    private String docName;
    private String docTitle;
    private String docUrl;
    private String docLocalPath;
    private int productId;


    public DocumentBean() {
    }


    public DocumentBean(int docId, String docType, String docTitle, String docName, String docUrl, String docLocalPath, int productId) {
        this.docId = docId;
        this.productId = productId;
        this.docType = docType;
        this.docName = docName;
        this.docUrl = docUrl;
        this.docLocalPath = docLocalPath;
        this.docTitle = docTitle;
    }

    public int getProductId() {
        return productId;
    }

    public void setProductId(int productId) {
        this.productId = productId;
    }

    public int getDocId() {
        return docId;
    }

    public void setDocId(int docId) {
        this.docId = docId;
    }

    public String getDocType() {
        return docType;
    }

    public void setDocType(String docType) {
        this.docType = docType;
    }

    public String getDocName() {
        return docName;
    }

    public void setDocName(String docName) {
        this.docName = docName;
    }

    public String getDocTitle() {
        return docTitle;
    }

    public void setDocTitle(String docTitle) {
        this.docTitle = docTitle;
    }

    public String getDocUrl() {
        return docUrl;
    }

    public void setDocUrl(String docUrl) {
        this.docUrl = docUrl;
    }

    public String getDocLocalPath() {
        return docLocalPath;
    }

    public void setDocLocalPath(String docLocalPath) {
        this.docLocalPath = docLocalPath;
    }
}
